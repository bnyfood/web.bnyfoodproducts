<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upload_secure (Defender-first + pre-move audit + retention)
 * -----------------------------------------------------------
 * - CI3 library for Windows Server + IIS + Microsoft Defender.
 * - GUARANTEED audit JSON:
 *     * Create a pre-move audit record immediately after basic validation,
 *       before calling move_uploaded_file().
 *     * Update the same audit record to "moved", "move_failed", or "flagged".
 * - Defender-first: malicious payload is never kept by us; Defender quarantines.
 * - Retention: purge audit JSON older than N days (default 30).
 *
 * Returns (backward compatible):
 *   ['file_name'=>string, 'is_upload'=>0|1, 'ok'=>bool, 'status'=>'accepted|flagged|error', 'hash'=>string, 'mime'=>string, 'reason'=>string]
 */
class Upload_secure {

    /** @var CI_Controller */
    protected $CI;

    protected $cfg = [
        // Storage (outside webroot)
        'storage_root'        => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads',
        'upload_tmp_dir'      => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\tmp',
        'psp_inbox_dir'       => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\psp',
        // Audit records (metadata only; no payload)
        'quarantine_dir'      => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\quarantine_audit',

        // Defender
        'defender_cli'        => 'C:\\Program Files\\Windows Defender\\MpCmdRun.exe',

        // Policy
        'max_bytes'           => 25 * 1024 * 1024, // 25 MB
        'allowed_ext'         => ['pdf','jpg','jpeg','png','webp','txt','csv','doc','docx','xls','xlsx','zip'],
        'blocked_ext'         => ['php','phtml','php2','php3','php4','php5','php7','php8','pht','phar','asp','aspx','jsp','exe','dll','com','scr','vbs','js','mjs','ps1','bat','cmd','sh','pl','cgi'],
        'deny_double_ext'     => false, // allow dotted version in filenames; still block dangerous secondaries

        // Logging
        'log_file'            => 'C:\\inetpub\\storage\\bnyfoodproducts\\logs\\upload_scan.log',

        // Audit & retention
        'write_audit_meta'    => true,
        'quarantine_retention_days' => 30,
        'defender_code1_as_clean' => true,  
    ];

    public function __construct(array $params = [])
    {
        $this->CI =& get_instance();
        $this->cfg = array_merge($this->cfg, $params);
        foreach (['storage_root','upload_tmp_dir','psp_inbox_dir','quarantine_dir'] as $d) {
            $this->ensureDir($this->cfg[$d]);
        }
        $this->ensureDir(dirname($this->cfg['log_file']));
    }

    /**
     * Main entry
     * @param string $field HTML input name
     * @return array
     */
    public function upload_file(string $field): array
    {
        $out = ['file_name'=>'', 'is_upload'=>0, 'ok'=>false, 'status'=>'error', 'hash'=>'', 'mime'=>'', 'reason'=>''];

        // 0) Basic gates
        if (empty($_FILES[$field]) || !isset($_FILES[$field]['tmp_name'])) {
            $this->auditAbnormal('no file uploaded', ['field'=>$field]);
            return $this->fail($out, 'No file uploaded');
        }
        $f = $_FILES[$field];
        if (!is_uploaded_file($f['tmp_name'])) {
            $this->auditAbnormal('not an uploaded file', ['tmp_name'=>$f['tmp_name'] ?? '']);
            return $this->fail($out, 'Not an uploaded file');
        }
        if ((int)$f['size'] <= 0 || (int)$f['size'] > $this->cfg['max_bytes']) {
            $this->auditAbnormal('file size out of policy', ['size'=>(int)$f['size']]);
            return $this->fail($out, 'File size out of policy');
        }

        // 1) Pre-hash (in case Defender quarantines before move)
        $preHash = '';
        if (is_file($f['tmp_name'])) {
            $preHash = @hash_file('sha256', $f['tmp_name']) ?: '';
        }

        // 2) Name & extension policy
        $origName = $this->sanitizeBaseName($f['name']);
        $ext = $this->safeExt($origName);
        if ($ext === '' || in_array($ext, $this->cfg['blocked_ext'], true)) {
            $this->auditAbnormal('blocked extension', compact('origName','ext','preHash'));
            return $this->fail($out, 'Blocked extension', ['ext'=>$ext]);
        }
        if (!in_array($ext, $this->cfg['allowed_ext'], true)) {
            $this->auditAbnormal('not in allowlist', compact('origName','ext','preHash'));
            return $this->fail($out, 'Not in allowlist', ['ext'=>$ext]);
        }
        if ($this->cfg['deny_double_ext'] && $this->hasDoubleExtension($origName)) {
            $this->auditAbnormal('double extension detected', compact('origName','ext','preHash'));
            return $this->fail($out, 'Double extension detected');
        }

        // 3) MIME policy
        $mime = $this->mimeDetect($f['tmp_name']);
        if (!$this->mimeAllowed($ext, $mime)) {
            $this->auditAbnormal('mime not allowed', compact('origName','ext','mime','preHash'));
            return $this->fail($out, 'MIME not allowed ('.$mime.')', ['mime'=>$mime,'ext'=>$ext]);
        }

        // 4) Create *pre-move* audit
        $auditId = $this->auditCreate([
            'state'      => 'pre_move',
            'orig_name'  => $origName,
            'mime'       => $mime,
            'size'       => (int)$f['size'],
            'sha256'     => $preHash,
            'tmp_path'   => $f['tmp_name'],
            'tmp_exists' => is_file($f['tmp_name']),
        ]);

        // 5) Move to tmp (random name + keep ext)
        $newName   = $this->randomName($ext);
        $tmpTarget = rtrim($this->cfg['upload_tmp_dir'],'\\/') . DIRECTORY_SEPARATOR . $newName;

        if (!@move_uploaded_file($f['tmp_name'], $tmpTarget)) {
            $existsSrc = is_file($f['tmp_name']);
            if (!$existsSrc) {
                // Defender real-time quarantined before move
                $this->auditUpdate($auditId, [
                    'state'   => 'flagged',
                    'reason'  => 'Defender real-time quarantined before move',
                    'code'    => 2,
                ]);
                $out['status'] = 'flagged';
                $out['reason'] = 'Defender real-time quarantined';
                return $out;
            }
            $this->auditUpdate($auditId, [
                'state'   => 'move_failed',
                'reason'  => 'Cannot move to tmp',
                'dst'     => $tmpTarget,
                'writable_dst' => is_writable(dirname($tmpTarget)),
                'last_error'   => error_get_last(),
            ]);
            return $this->fail($out, 'Cannot move to tmp', ['to'=>$tmpTarget]);
        }

        // 6) Image structure check
        if (in_array($ext, ['jpg','jpeg','png','webp'], true) && @getimagesize($tmpTarget) === false) {
            @unlink($tmpTarget);
            $this->auditUpdate($auditId, ['state'=>'error','reason'=>'bad image structure']);
            return $this->fail($out, 'Bad image structure');
        }

        // 7) Defender scan
        $scan = $this->defender_scan($tmpTarget);
        $this->log('info', 'scan result', ['file'=>$newName, 'mime'=>$mime, 'scan'=>$scan['reason'], 'code'=>$scan['code']]);

        if ($scan['clean'] === true) {
            // Accept -> PSP
            $final = rtrim($this->cfg['psp_inbox_dir'],'\\/') . DIRECTORY_SEPARATOR . $newName;
            if (!@rename($tmpTarget, $final)) {
                @unlink($tmpTarget);
                $this->auditUpdate($auditId, ['state'=>'error','reason'=>'cannot move to psp','dst'=>$final]);
                return $this->fail($out, 'Cannot move to PSP', ['to'=>$final]);
            }

            $hash = @hash_file('sha256', $final);
            $this->auditUpdate($auditId, ['state'=>'accepted','saved_as'=>$newName,'psp_path'=>$final,'sha256_final'=>$hash]);
            $this->log('info', 'accepted', [
                'file'=>$newName, 'hash'=>$hash, 'mime'=>$mime, 'size'=>@filesize($final),
                'orig'=>$origName
            ] + $this->requestContext());

            $out['ok'] = true;
            $out['status'] = 'accepted';
            $out['file_name'] = $newName;
            $out['is_upload'] = 1;
            $out['hash'] = $hash;
            $out['mime'] = $mime;
            return $out;
        }

        // Flagged/uncertain
        if (is_file($tmpTarget)) { @unlink($tmpTarget); }
        $this->auditUpdate($auditId, [
            'state'   => 'flagged',
            'reason'  => $scan['reason'].' (via CLI)',
            'code'    => $scan['code'],
            'saved_as'=> $newName,
        ]);

        $out['status'] = 'flagged';
        $out['reason'] = $scan['reason'];
        return $out;
    }

    // ---------------- Audit helpers ----------------
    protected function auditCreate(array $meta): string {
        if (!$this->cfg['write_audit_meta']) return '';
        $meta = $meta + $this->requestContext() + ['time'=>date('c')];
        $id = date('Ymd_His') . '_' . bin2hex(random_bytes(4));
        $path = rtrim($this->cfg['quarantine_dir'],'\\/') . DIRECTORY_SEPARATOR . $id . '.json';
        @file_put_contents($path, json_encode($meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        $this->purgeOldQuarantine();
        return $path;
    }
    protected function auditUpdate(string $auditPath, array $fields): void {
        if (!$this->cfg['write_audit_meta'] || !$auditPath) return;
        $data = [];
        if (is_file($auditPath)) {
            $json = @file_get_contents($auditPath);
            $data = $json ? (json_decode($json, true) ?: []) : [];
        }
        $data = array_merge($data, $fields, ['time_updated'=>date('c')]);
        @file_put_contents($auditPath, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }
    protected function auditAbnormal(string $reason, array $extra = []): void {
        $this->auditCreate(array_merge(['state'=>'abnormal','reason'=>$reason], $extra));
    }
    protected function purgeOldQuarantine(): void {
        $days = (int)$this->cfg['quarantine_retention_days'];
        if ($days <= 0) return;
        $dir = rtrim($this->cfg['quarantine_dir'],'\\/') . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) return;
        $cut = time() - ($days * 86400);
        $files = @scandir($dir);
        if (!is_array($files)) return;
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $full = $dir . $f;
            if (!is_file($full)) continue;
            if (strtolower(pathinfo($full, PATHINFO_EXTENSION)) !== 'json') continue;
            $mtime = @filemtime($full);
            if ($mtime !== false && $mtime < $cut) { @unlink($full); $this->log('info', 'purged quarantine record', ['file'=>$f]); }
        }
    }

    // ---------------- Helpers ----------------
    protected function requestContext(): array {
        $ip = method_exists($this->CI->input ?? null, 'ip_address') ? $this->CI->input->ip_address() : ($_SERVER['REMOTE_ADDR'] ?? '');
        $ua = method_exists($this->CI->input ?? null, 'user_agent') ? $this->CI->input->user_agent() : ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $uid = '';
        if (isset($this->CI->session) && method_exists($this->CI->session, 'userdata')) {
            $uid = $this->CI->session->userdata('user_id') ?? '';
        }
        return ['ip'=>$ip, 'user_agent'=>$ua, 'user_id'=>$uid];
    }

    protected function fail(array $out, string $msg, array $ctx=[]): array {
        $this->log('warn', $msg, $ctx);
        $out['status'] = 'error';
        $out['reason'] = $msg;
        return $out;
    }

    protected function ensureDir(string $dir): void {
        if (!is_dir($dir)) { @mkdir($dir, 0770, true); }
        $marker = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . 'NO-WEB.README';
        if (!file_exists($marker)) { @file_put_contents($marker, "Storage only."); }
    }

    protected function sanitizeBaseName(string $name): string {
        $name = str_replace(["\0","\r","\n"], '', $name);
        $name = basename($name);
        $name = preg_replace('/[^\PC\s]/u', '', $name);
        return $name;
    }
    protected function safeExt(string $name): string {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        return preg_replace('/[^a-z0-9]+/', '', $ext);
    }
    protected function hasDoubleExtension(string $name): bool {
        $lower = strtolower($name);
        return (bool)preg_match('/\.(php|phtml|phar|asp|aspx|jsp|js|vbs|ps1|bat|cmd|sh)(?:[^a-z0-9]|$)/i', $lower);
    }
    protected function mimeDetect(string $path): string {
        if (function_exists('finfo_open')) {
            $f = @finfo_open(FILEINFO_MIME_TYPE);
            if ($f) {
                $m = @finfo_file($f, $path) ?: 'application/octet-stream';
                @finfo_close($f);
                return $m;
            }
        }
        if (function_exists('mime_content_type')) {
            return @mime_content_type($path) ?: 'application/octet-stream';
        }
        return 'application/octet-stream';
    }
    protected function mimeAllowed(string $ext, string $mime): bool {
        $mime = strtolower(trim($mime));
        $map = [
            'pdf'  => ['application/pdf'],
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
            'webp' => ['image/webp'],
            'txt'  => ['text/plain','application/octet-stream'],
            'csv'  => ['text/csv','application/csv','text/plain','application/vnd.ms-excel'],
            'doc'  => ['application/msword','application/ms-word','application/vnd.ms-word','application/octet-stream'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/zip','application/octet-stream'],
            'xls'  => ['application/vnd.ms-excel','application/msexcel','application/xls','application/x-msexcel','application/vnd.ms-office','application/octet-stream'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','application/zip','application/x-zip-compressed','application/octet-stream'],
            'zip'  => ['application/zip','application/x-zip-compressed','multipart/x-zip'],
        ];
        if (!isset($map[$ext])) return false;
        foreach ($map[$ext] as $ok) {
            if (strpos($mime, $ok) === 0) return true;
        }
        return false;
    }
    protected function randomName(string $ext): string {
        return bin2hex(random_bytes(16)) . ($ext ? ".$ext" : '');
    }

    protected function defender_scan(string $file): array {
        $bin = $this->cfg['defender_cli'];
        if (!is_file($bin)) return ['clean'=>false, 'reason'=>'Defender CLI not found', 'code'=>-1, 'raw'=>''];

        $cmd = '"' . $bin . '" -Scan -ScanType 3 -File "' . $file . '"';
        $res = $this->runCmd($cmd);

        $stdout = strtolower($res['stdout'] ?? '');
        $stderr = strtolower($res['stderr'] ?? '');
        $text   = $stdout . "\n" . $stderr;
        $code   = (int)($res['code'] ?? 1);

        $noThreat = (strpos($text, 'no threats') !== false) || (strpos($text, 'no infections') !== false);
        $threat   = (strpos($text, 'threat') !== false) || (strpos($text, 'infect') !== false);

        // ✅ ปรับ logic: treat code=1 as clean เมื่อเปิดคอนฟิก defender_code1_as_clean
        if ($noThreat || ($code === 1 && ! $threat && !empty($this->cfg['defender_code1_as_clean']))) {
            return ['clean'=>true, 'reason'=>'Defender clean', 'code'=>$code, 'raw'=>$res['stdout'] ?? ''];
        }
        if ($threat || $code === 2) {
            return ['clean'=>false, 'reason'=>'Defender flagged', 'code'=>$code, 'raw'=>$res['stdout'] ?? ''];
        }
        return ['clean'=>false, 'reason'=>"Defender uncertain (code={$code})", 'code'=>$code, 'raw'=>$res['stdout'] ?? ''];
    }


    protected function runCmd(string $cmd): array {
        $desc = [1=>['pipe','w'], 2=>['pipe','w']];
        $p = @proc_open($cmd, $desc, $pipes, null, null);
        if (!is_resource($p)) return ['code'=>-1,'stdout'=>'','stderr'=>'proc_open failed'];
        $out = stream_get_contents($pipes[1]); @fclose($pipes[1]);
        $err = stream_get_contents($pipes[2]); @fclose($pipes[2]);
        $code = @proc_close($p);
        return ['code'=>$code,'stdout'=>$out,'stderr'=>$err];
    }

    protected function log(string $level, string $msg, array $ctx = []): void {
        $line = sprintf("[%s] [%s] %s %s\r\n",
            date('Y-m-d H:i:s'), strtoupper($level), $msg,
            $ctx ? json_encode($ctx, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($this->cfg['log_file'], $line, FILE_APPEND);
    }

}
