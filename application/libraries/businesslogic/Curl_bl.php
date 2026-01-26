<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curl_bl{
	public function __construct(){
		$this->CI =& get_instance();
		
		$this->CI->load->library('util/json_util');
		$this->CI->load->helper('cookie');
		$this->CI->load->library('util/encryption_util');
	}

	
	
	function call_by_url($url,$param){
		
		$ch = curl_init();

		$headers = array(
	    'Accept: application/json',
	    'Content-Type: application/json',
	    'token:'
	    );

		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
	   
	}
	/*<script>
			//alert("132");
		  var element3 = document.getElementById("loader");
		  element3.classList.remove("hidden");
		  //element.classList.add("hidden");
		</script>*/

	function CallApi_v1($method,$url,$param=null){

		$this->CI->load->view('spinner_in');

		//$token = $this->CI->session->userdata('token');
		//echo ">>>>".$token;
		$token_en = get_cookie(COOKIE_PREFIX.'token');
		$token = $this->CI->encryption_util->decrypt_ssl($token_en);

		$ch = curl_init();

	    $headers = array(
	    'token:'.$token
	    );

	    //print_r($headers);

	    $url_api = BNY_API_URL.$url;

	    //echo $url_api;

		curl_setopt($ch, CURLOPT_URL,$url_api);
 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($method == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Max wait 10 sec.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$server_output = curl_exec ($ch);
		if ($server_output === false)
		{
		    print_r('Curl error: ' . curl_error($ch));
		}

		$info = curl_getinfo($ch);
		//echo '<br>Took ' . $info['total_time'] . ' seconds to transfer a request to ' . $info['url'];

		curl_close ($ch);

		//show error
		//echo "--------------<br>";
		//print_r($server_output);
		//echo "--------------<br>";

		$data_de = json_decode($server_output,true);


		if($data_de['Status'] == "FailToken"){
			redirect(base_url().'users/logout','refresh');

		}else{
			$data['curl_time'] = ($info['total_time']*1000)+500;
			$this->CI->load->view('spinner_out',$data);
		}

		return $data_de;
	}

	function CallApi($method, $url, $param = null)
	{

		//$this->CI->load->view('spinner_in');

		// --- เตรียม token ตามของเดิม ---
		// $token = $this->CI->session->userdata('token');
		// $token_en = get_cookie(COOKIE_PREFIX.'token');
		// $token = $this->ci->encryption_util->decrypt_ssl($token_en);
		$token = $this->CI->encryption_util->decrypt_ssl(get_cookie(COOKIE_PREFIX.'token'));

		$ch = curl_init();
		if ($ch === false) {
			throw new \RuntimeException('Cannot init curl');
		}

		$headers = [
			'token: ' . $token,
			// ใส่เพิ่มถ้า API ต้องการส่ง JSON เสมอ:
			// 'Content-Type: application/json',
			// 'Accept: application/json',
		];

		$url_api = BNY_API_URL . $url;

		// ---- ตั้งค่า cURL (ไม่ใช้ RETURNTRANSFER) ----
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url_api,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT        => 300,   // ไฟล์ใหญ่ให้เวลามากหน่อย
			CURLOPT_HEADER         => false,
			CURLOPT_ENCODING       => '',    // เปิด gzip/deflate อัตโนมัติ (ช่วยลดขนาดมาก)
			CURLOPT_RETURNTRANSFER => false, // ห้ามบัฟเฟอร์ทั้งก้อนในแรม
		]);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		}
		// ---- สตรีม response ลงไฟล์ชั่วคราวแทนการเก็บในหน่วยความจำ ----
		$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'curl_' . uniqid('', true) . '.tmp';
		$fp = fopen($tmpFile, 'w+b');
		if ($fp === false) {
			curl_close($ch);
			throw new \RuntimeException('Cannot open temp file: ' . $tmpFile);
		}

		$bytes = 0;
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($fp, &$bytes) {
			//error data show here!!!
			//print_r($data);
			$len = strlen($data);
			$bytes += $len;
			$w = fwrite($fp, $data);
			return ($w === false) ? 0 : $w;
		});

		$ok = curl_exec($ch);
		//print_r($ok);
		if ($ok === false) {
			$err = curl_error($ch);
			curl_close($ch);
			fclose($fp);
			@unlink($tmpFile);
			// ให้พฤติกรรมเหมือนเดิม: print_r แล้วหยุด
			print_r('Curl error: ' . $err);
			return ['Status' => 'CurlError', 'Message' => $err];
		}

		$info = curl_getinfo($ch); // เอา total_time, http_code, content_type
		//print_r($info);
		curl_close($ch);
		fflush($fp);
		fseek($fp, 0);

		//show error
		/*$err = curl_error($ch);
		echo "--------------<br>";
		print_r($err);
		echo "--------------<br>";
		*/

		// ---- ตรวจ HTTP code ก่อน ----
		$http = (int)($info['http_code'] ?? 0);
		if ($http < 200 || $http >= 300) {
			// ดึงข้อความสั้น ๆ เพื่อดีบัก ถ้าไม่ใหญ่
			$peek = ($bytes <= 1024 * 1024) ? stream_get_contents($fp) : '';
			fclose($fp);
			@unlink($tmpFile);
			return [
				'Status'   => 'HttpError',
				'Code'     => $http,
				'Preview'  => $peek,
				'curl_time'=> (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null),
			];
		}

		// ---- ตัดสินใจอ่านเข้าแรม (รักษาพฤติกรรมเดิมที่ต้อง json_decode) ----
		// ถ้าไฟล์ยังใหญ่ ให้ขยาย memory_limit ชั่วคราว
		// (ป้องกัน json_decode ใช้แรมช่วง peak)
		$INLINE_LIMIT = 200 * 1024 * 1024; // 200MB หลังบีบอัดแล้วมักโอเค
		if ($bytes > $INLINE_LIMIT) {
			// ดันเพดานสัก 2GB เฉพาะเคสนี้
			$cur = ini_get('memory_limit');
			if ($cur && $cur !== '-1') {
				@ini_set('memory_limit', '2048M');
			}
		}

		$json = stream_get_contents($fp); // อ่านทั้งก้อน *หลัง* ที่สตรีมลงไฟล์แล้ว
		fclose($fp);
		@unlink($tmpFile);

		$data_de = json_decode($json, true);

		// ถ้า decode ไม่ได้ ให้คืนข้อมูลวัตถุดิบไว้ดีบัก
		if ($data_de === null && json_last_error() !== JSON_ERROR_NONE) {
			return [
				'Status'    => 'JsonError',
				'JsonError' => json_last_error_msg(),
				'Size'      => $bytes,
				'curl_time' => isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null,
				// 'Raw'    => substr($json, 0, 2000), // จะเปิดก็ได้ แต่อาจใหญ่
			];
		}

		// ---- รักษาพฤติกรรมเดิม: เช็ค FailToken และตั้ง curl_time ----
		if (isset($data_de['Status']) && $data_de['Status'] === 'FailToken') {
			// เดิม: redirect(base_url().'users/logout','refresh');
			redirect(base_url() . 'users/logout', 'refresh');
			return $data_de; // เผื่อถึง caller
		} else {
			$data_de['curl_time'] = (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : 0);
			//$this->CI->load->view('spinner_out',$data);
			return $data_de;
		}
	}

	function CallApiNospi($method, $url, $param = null)
	{

		// --- เตรียม token ตามของเดิม ---
		// $token = $this->CI->session->userdata('token');
		// $token_en = get_cookie(COOKIE_PREFIX.'token');
		// $token = $this->ci->encryption_util->decrypt_ssl($token_en);
		$token = $this->CI->encryption_util->decrypt_ssl(get_cookie(COOKIE_PREFIX.'token'));

		$ch = curl_init();
		if ($ch === false) {
			throw new \RuntimeException('Cannot init curl');
		}

		$headers = [
			'token: ' . $token,
			// ใส่เพิ่มถ้า API ต้องการส่ง JSON เสมอ:
			// 'Content-Type: application/json',
			// 'Accept: application/json',
		];

		$url_api = BNY_API_URL . $url;

		// ---- ตั้งค่า cURL (ไม่ใช้ RETURNTRANSFER) ----
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url_api,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT        => 300,   // ไฟล์ใหญ่ให้เวลามากหน่อย
			CURLOPT_HEADER         => false,
			CURLOPT_ENCODING       => '',    // เปิด gzip/deflate อัตโนมัติ (ช่วยลดขนาดมาก)
			CURLOPT_RETURNTRANSFER => false, // ห้ามบัฟเฟอร์ทั้งก้อนในแรม
		]);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		}

		// ---- สตรีม response ลงไฟล์ชั่วคราวแทนการเก็บในหน่วยความจำ ----
		$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'curl_' . uniqid('', true) . '.tmp';
		$fp = fopen($tmpFile, 'w+b');
		if ($fp === false) {
			curl_close($ch);
			throw new \RuntimeException('Cannot open temp file: ' . $tmpFile);
		}

		$bytes = 0;
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($fp, &$bytes) {
			//error data show here!!!
			//print_r($data);
			$len = strlen($data);
			$bytes += $len;
			$w = fwrite($fp, $data);
			return ($w === false) ? 0 : $w;
		});

		$ok = curl_exec($ch);
		if ($ok === false) {
			$err = curl_error($ch);
			curl_close($ch);
			fclose($fp);
			@unlink($tmpFile);
			// ให้พฤติกรรมเหมือนเดิม: print_r แล้วหยุด
			print_r('Curl error: ' . $err);
			return ['Status' => 'CurlError', 'Message' => $err];
		}

		$info = curl_getinfo($ch); // เอา total_time, http_code, content_type
		curl_close($ch);
		fflush($fp);
		fseek($fp, 0);

		// ---- ตรวจ HTTP code ก่อน ----
		$http = (int)($info['http_code'] ?? 0);
		if ($http < 200 || $http >= 300) {
			// ดึงข้อความสั้น ๆ เพื่อดีบัก ถ้าไม่ใหญ่
			$peek = ($bytes <= 1024 * 1024) ? stream_get_contents($fp) : '';
			fclose($fp);
			@unlink($tmpFile);
			return [
				'Status'   => 'HttpError',
				'Code'     => $http,
				'Preview'  => $peek,
				'curl_time'=> (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null),
			];
		}

		// ---- ตัดสินใจอ่านเข้าแรม (รักษาพฤติกรรมเดิมที่ต้อง json_decode) ----
		// ถ้าไฟล์ยังใหญ่ ให้ขยาย memory_limit ชั่วคราว
		// (ป้องกัน json_decode ใช้แรมช่วง peak)
		$INLINE_LIMIT = 200 * 1024 * 1024; // 200MB หลังบีบอัดแล้วมักโอเค
		if ($bytes > $INLINE_LIMIT) {
			// ดันเพดานสัก 2GB เฉพาะเคสนี้
			$cur = ini_get('memory_limit');
			if ($cur && $cur !== '-1') {
				@ini_set('memory_limit', '2048M');
			}
		}

		$json = stream_get_contents($fp); // อ่านทั้งก้อน *หลัง* ที่สตรีมลงไฟล์แล้ว
		fclose($fp);
		@unlink($tmpFile);

		$data_de = json_decode($json, true);

		// ถ้า decode ไม่ได้ ให้คืนข้อมูลวัตถุดิบไว้ดีบัก
		if ($data_de === null && json_last_error() !== JSON_ERROR_NONE) {
			return [
				'Status'    => 'JsonError',
				'JsonError' => json_last_error_msg(),
				'Size'      => $bytes,
				'curl_time' => isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null,
				// 'Raw'    => substr($json, 0, 2000), // จะเปิดก็ได้ แต่อาจใหญ่
			];
		}

		// ---- รักษาพฤติกรรมเดิม: เช็ค FailToken และตั้ง curl_time ----
		if (isset($data_de['Status']) && $data_de['Status'] === 'FailToken') {
			// เดิม: redirect(base_url().'users/logout','refresh');
			redirect(base_url() . 'users/logout', 'refresh');
			return $data_de; // เผื่อถึง caller
		} else {
			$data_de['curl_time'] = (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : 0);
			return $data_de;
		}
	}

	function CallApiNospi_v1($method,$url,$param=null){


		$shopid_en = $this->CI->session->userdata(SESSION_PREFIX.'shop_id');
		$shopid = $this->CI->encryption_util->decrypt_ssl($shopid_en);

		//echo "shopid>>".$shopid;
		if($shopid == ""){
			redirect(base_url().'users/logout','refresh');
		}

		//$this->CI->load->view('spinner_in');

		//$token = $this->CI->session->userdata('token');
		//echo ">>>>".$token;
		$token_en = get_cookie(COOKIE_PREFIX.'token');
		$token = $this->CI->encryption_util->decrypt_ssl($token_en);

		$ch = curl_init();

		/*$headers = array(
	    'Authorization: application/json',
	    'token:',$token
	    );*/

	    $headers = array(
	    'token:'.$token
	    );

	    /*$headers = array(
	    'Authorization: application/json',
	    'token:'.$token
	    );*/

	    //print_r($headers);

	    $url_api = BNY_API_URL.$url;

		curl_setopt($ch, CURLOPT_URL,$url_api);
 

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($method == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__."/cookies/cookie.txt");
		//curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__."/cookies/cookie.txt");

		$server_output = curl_exec ($ch);
		if ($server_output === false)
		{
		    print_r('Curl error: ' . curl_error($ch));
		}

		$info = curl_getinfo($ch);
		//echo '<br>Took ' . $info['total_time'] . ' seconds to transfer a request to ' . $info['url'];




		curl_close ($ch);

		//show error
		//print_r($server_output);

		$data_de = json_decode($server_output,true);

		//print_r($data_de);

		if($data_de['Status'] == "FailToken"){
			redirect(base_url().'users/logout','refresh');
		}else{
			$data['curl_time'] = ($info['total_time']*1000)+500;
			//$this->CI->load->view('spinner_out',$data);
		}

	
		return $data_de;
	}

	function call_curl($method,$url,$param=null){
		
		//$token = $this->CI->session->userdata('token');

		$token_en = get_cookie(COOKIE_PREFIX.'token');
		$token = $this->CI->encryption_util->decrypt_ssl($token_en);

		$ch = curl_init();

		/*$headers = array(
	    'Authorization: application/json',
	    'token:',$token
	    );*/
	    $headers = array(
	    'token:'.$token
	    );

	    //print_r($headers);

	    $url_api = BNY_API_URL.$url;

		curl_setopt($ch, CURLOPT_URL,$url_api);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($method == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);

		return json_decode($server_output,true);
	   
	}

	function call_curl_notoken($method, $url, $param = null)
	{
		// --- เตรียม token ตามของเดิม ---
		// $token = $this->CI->session->userdata('token');
		// $token_en = get_cookie(COOKIE_PREFIX.'token');
		// $token = $this->ci->encryption_util->decrypt_ssl($token_en);
		//$token = $this->CI->encryption_util->decrypt_ssl(get_cookie(COOKIE_PREFIX.'token'));

		$ch = curl_init();
		if ($ch === false) {
			throw new \RuntimeException('Cannot init curl');
		}

		$headers = [
			'Authorization: application/json'
		];

		$url_api = BNY_API_URL . $url;

		// ---- ตั้งค่า cURL (ไม่ใช้ RETURNTRANSFER) ----
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url_api,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT        => 300,   // ไฟล์ใหญ่ให้เวลามากหน่อย
			CURLOPT_HEADER         => false,
			CURLOPT_ENCODING       => '',    // เปิด gzip/deflate อัตโนมัติ (ช่วยลดขนาดมาก)
			CURLOPT_RETURNTRANSFER => false, // ห้ามบัฟเฟอร์ทั้งก้อนในแรม
		]);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		}

		// ---- สตรีม response ลงไฟล์ชั่วคราวแทนการเก็บในหน่วยความจำ ----
		$path = APP_STORE_PATH."\cache\curl";
		$tmpFile = $path . DIRECTORY_SEPARATOR . 'curl_' . uniqid('', true) . '.tmp';
		
		//echo "----------------";
		//echo $tmpFile;
		//echo "----------------";
		$fp = fopen($tmpFile, 'w+b');
		if ($fp === false) {
			curl_close($ch);
			throw new \RuntimeException('Cannot open temp file: ' . $tmpFile);
		}

		$bytes = 0;
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($fp, &$bytes) {
			
			print_r($data);
			
			$len = strlen($data);
			$bytes += $len;
			$w = fwrite($fp, $data);
			return ($w === false) ? 0 : $w;
		});

		$ok = curl_exec($ch);
		if ($ok === false) {
			$err = curl_error($ch);
			curl_close($ch);
			fclose($fp);
			@unlink($tmpFile);
			// ให้พฤติกรรมเหมือนเดิม: print_r แล้วหยุด
			print_r('Curl error: ' . $err);
			return ['Status' => 'CurlError', 'Message' => $err];
		}

		$info = curl_getinfo($ch); // เอา total_time, http_code, content_type
		curl_close($ch);
		fflush($fp);
		fseek($fp, 0);

		// ---- ตรวจ HTTP code ก่อน ----
		$http = (int)($info['http_code'] ?? 0);
		if ($http < 200 || $http >= 300) {
			// ดึงข้อความสั้น ๆ เพื่อดีบัก ถ้าไม่ใหญ่
			$peek = ($bytes <= 1024 * 1024) ? stream_get_contents($fp) : '';
			fclose($fp);
			@unlink($tmpFile);
			return [
				'Status'   => 'HttpError',
				'Code'     => $http,
				'Preview'  => $peek,
				'curl_time'=> (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null),
			];
		}

		// ---- ตัดสินใจอ่านเข้าแรม (รักษาพฤติกรรมเดิมที่ต้อง json_decode) ----
		// ถ้าไฟล์ยังใหญ่ ให้ขยาย memory_limit ชั่วคราว
		// (ป้องกัน json_decode ใช้แรมช่วง peak)
		$INLINE_LIMIT = 200 * 1024 * 1024; // 200MB หลังบีบอัดแล้วมักโอเค
		if ($bytes > $INLINE_LIMIT) {
			// ดันเพดานสัก 2GB เฉพาะเคสนี้
			$cur = ini_get('memory_limit');
			if ($cur && $cur !== '-1') {
				@ini_set('memory_limit', '2048M');
			}
		}

		$json = stream_get_contents($fp); // อ่านทั้งก้อน *หลัง* ที่สตรีมลงไฟล์แล้ว
		fclose($fp);
		@unlink($tmpFile);

		$data_de = json_decode($json, true);

		// ถ้า decode ไม่ได้ ให้คืนข้อมูลวัตถุดิบไว้ดีบัก
		if ($data_de === null && json_last_error() !== JSON_ERROR_NONE) {
			return [
				'Status'    => 'JsonError',
				'JsonError' => json_last_error_msg(),
				'Size'      => $bytes,
				'curl_time' => isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : null,
				// 'Raw'    => substr($json, 0, 2000), // จะเปิดก็ได้ แต่อาจใหญ่
			];
		}

		// ---- รักษาพฤติกรรมเดิม: เช็ค FailToken และตั้ง curl_time ----
		if (isset($data_de['Status']) && $data_de['Status'] === 'FailToken') {
			// เดิม: redirect(base_url().'users/logout','refresh');
			redirect(base_url() . 'users/logout', 'refresh');
			return $data_de; // เผื่อถึง caller
		} else {
			$data_de['curl_time'] = (isset($info['total_time']) ? ($info['total_time'] * 1000) + 500 : 0);
			return $data_de;
		}
	}



	function call_curl_notoken_v1($method,$url,$param=null){
		
		$ch = curl_init();

		$headers = array(
	   	 'Authorization: application/json'
	    );

	    $url_api = BNY_API_URL.$url;

		curl_setopt($ch, CURLOPT_URL,$url_api);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


		// CURLOPT_SSL_VERIFYHOST=>false,
            //CURLOPT_SSL_VERIFYPEER=>false,
		if($method == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		if ($server_output === false)
		{
		    print_r('Curl error: ' . curl_error($ch));
		}
		curl_close ($ch);
		//print_r($server_output);

		return json_decode($server_output,true);
	   
	}

	function curl_solr($url,$data){

		$ch = curl_init($url);

		$data_string = json_encode($data);

		//curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		$response = curl_exec($ch);

		print_r($response);

		curl_close ($ch);

	}

	function curl_solr_get($url){

		$ch = curl_init();


		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json; charset=utf-8','Content-type: application/json'));


		$response = curl_exec($ch);
		
		curl_close ($ch);

		return $response;

	}


	function curl_sms($msisdn,$message,$sender,$force){


		require_once __DIR__ . "/../../../resources/api/sms.php";

		$apiKey = SMS_API_KEY;
		$apiSecretKey = SMS_SECRET_KEY;

		$sms = new SMS($apiKey, $apiSecretKey); 

		$body = [
		    'msisdn' => $msisdn,
		    'message' => $message,
		     'sender' => $sender,
		    // 'scheduled_delivery' => '',
		     'force' => $force
		];
		$res = $sms->sendSMS($body);

		if ($res->httpStatusCode == 201) {
		    echo "Succes";
		    var_dump($res);
		} else {
		    echo "Error";
		    var_dump($res);
		}
	}
	
	
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */