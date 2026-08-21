<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';
require_once APPPATH . "/third_party/PHPExcel/IOFactory.php";

use Smalot\PdfParser\Parser;

class Managepdf extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('util/view_util');
        $this->load->library('businesslogic/auth_bl');
        $this->auth_bl->check_session_exists();
        $this->load->helper(array('form', 'url', 'file'));
    }

    public function index() {
        $data = array();
        $arr_css = array();
        $arr_js = array();
        $arr_input = array('title' => 'PDF to Excel');

        $this->view_util->load_view_main('managepdf/upload_pdf', $data, $arr_css, $arr_js, $arr_input);
    }

    public function upload_process() {
        @ini_set('max_execution_time', '0');
        @ini_set('max_input_time', '3600');
        @set_time_limit(0);

        $upload_path = 'C:/inetpub/storage/bnyfoodproducts/uploads/xls/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        if (empty($_FILES['pdf_files']['name'][0])) {
            $this->session->set_flashdata('error', 'กรุณาเลือกไฟล์ PDF');
            redirect('managepdf');
            return;
        }

        $files = $_FILES['pdf_files'];
        $file_count = count($files['name']);
        $uploaded_files = array();

        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION)) !== 'pdf') continue;

            $new_name = 'pdf_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.pdf';
            $dest = $upload_path . $new_name;

            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                $uploaded_files[] = $dest;
            }
        }

        if (empty($uploaded_files)) {
            $this->session->set_flashdata('error', 'ไม่สามารถ upload ไฟล์ได้ กรุณาลองใหม่');
            redirect('managepdf');
            return;
        }

        try {
            $results = $this->parse_batch_receipts($uploaded_files);

            foreach ($uploaded_files as $f) {
                @unlink($f);
            }

            if (empty($results)) {
                $this->session->set_flashdata('error', 'ไม่พบข้อมูล Receipt ในไฟล์ PDF ที่ upload (' . count($uploaded_files) . ' ไฟล์)');
                redirect('managepdf');
                return;
            }

            $this->generate_excel($results);

        } catch (\Exception $e) {
            foreach ($uploaded_files as $f) { @unlink($f); }
            $this->session->set_flashdata('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            redirect('managepdf');
        } catch (\Error $e) {
            foreach ($uploaded_files as $f) { @unlink($f); }
            $this->session->set_flashdata('error', 'PHP Error: ' . $e->getMessage());
            redirect('managepdf');
        }
    }

    private function parse_batch_receipts($uploaded_files) {
        $python = 'C:\\inetpub\\python311\\python.exe';
        $script = APPPATH . 'scripts/extract_pdf.py';
        $list_file = 'C:/inetpub/storage/bnyfoodproducts/uploads/xls/pdf_list_' . time() . '.json';

        if (file_exists($python) && file_exists($script)) {
            file_put_contents($list_file, json_encode($uploaded_files, JSON_UNESCAPED_SLASHES));
            $cmd = '"' . $python . '" "' . $script . '" --batch "' . $list_file . '" 2>&1';
            $output = shell_exec($cmd);
            @unlink($list_file);

            if (!empty($output)) {
                $json_start = strpos($output, '[');
                if ($json_start !== false) {
                    $output = substr($output, $json_start);
                }
                $data = json_decode($output, true);
                if (is_array($data) && !isset($data['error']) && !empty($data)) {
                    $results = array();
                    foreach ($data as $item) {
                        if (!empty($item['receipt_number'])) {
                            $results[] = array(
                                'receipt_number' => $item['receipt_number'],
                                'total_amount' => isset($item['total_amount']) ? $item['total_amount'] : ''
                            );
                        }
                    }
                    if (!empty($results)) {
                        return $results;
                    }
                }
            }
        }

        $results = array();
        foreach ($uploaded_files as $file_path) {
            $receipt = $this->parse_single_receipt($file_path);
            if (!empty($receipt['receipt_number'])) {
                $results[] = $receipt;
            }
        }

        return $results;
    }

    private function parse_single_receipt($file_path) {
        $result = array('receipt_number' => '', 'total_amount' => '');

        $python = 'C:\\inetpub\\python311\\python.exe';
        $script = APPPATH . 'scripts/extract_pdf.py';

        if (file_exists($python) && file_exists($script)) {
            $cmd = '"' . $python . '" "' . $script . '" "' . $file_path . '" 2>&1';
            $output = shell_exec($cmd);
            if (!empty($output)) {
                $data = json_decode($output, true);
                if (is_array($data) && !isset($data['error']) && !empty($data)) {
                    return array(
                        'receipt_number' => $data[0]['receipt_number'],
                        'total_amount' => isset($data[0]['total_amount']) ? $data[0]['total_amount'] : ''
                    );
                }
            }
        }

        $parser = new Parser();
        try {
            $pdf = $parser->parseFile($file_path);
            $pages = $pdf->getPages();
            if (!empty($pages)) {
                $text = $pages[0]->getText();
                $receipt_number = '';
                $total_amount = '';

                if (preg_match('/(TTSTHAC\d+)/i', $text, $m)) {
                    $receipt_number = $m[1];
                }
                $amount_patterns = array(
                    '/Total\s*Amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
                    '/Net\s*payable\s*amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
                );
                foreach ($amount_patterns as $pattern) {
                    if (preg_match($pattern, $text, $m)) {
                        $val = str_replace(',', '', trim($m[1]));
                        if (floatval($val) > 0) {
                            $total_amount = $val;
                            break;
                        }
                    }
                }
                if (!empty($receipt_number)) {
                    $result = array('receipt_number' => $receipt_number, 'total_amount' => $total_amount);
                }
            }
        } catch (\Exception $e) {}

        return $result;
    }

    private function parse_pdf_receipts($file_path) {
        $results = $this->parse_with_pymupdf($file_path);

        if (!empty($results)) {
            return $results;
        }

        $results = $this->parse_with_pdftotext($file_path);

        if (!empty($results)) {
            return $results;
        }

        $results = $this->parse_with_library($file_path);

        if (!empty($results)) {
            return $results;
        }

        return $this->parse_raw_pdf($file_path);
    }

    private function parse_with_pymupdf($file_path) {
        $python = 'C:\\inetpub\\python311\\python.exe';
        $script = APPPATH . 'scripts/extract_pdf.py';

        if (!file_exists($python) || !file_exists($script)) {
            return array();
        }

        $cmd = '"' . $python . '" "' . $script . '" "' . $file_path . '" 2>&1';
        $output = shell_exec($cmd);

        if (empty($output)) {
            return array();
        }

        $data = json_decode($output, true);

        if (is_null($data) || isset($data['error'])) {
            return array();
        }

        $results = array();
        foreach ($data as $item) {
            if (!empty($item['receipt_number'])) {
                $results[] = array(
                    'receipt_number' => $item['receipt_number'],
                    'total_amount' => isset($item['total_amount']) ? $item['total_amount'] : ''
                );
            }
        }

        return $results;
    }

    private function parse_raw_pdf($file_path) {
        $content = file_get_contents($file_path);
        $results = array();

        preg_match_all('/(TTSTHAC\d{10,})/i', $content, $receipt_matches);

        if (empty($receipt_matches[1])) {
            preg_match_all('/(TTS[A-Z]{2,4}\d{10,})/i', $content, $receipt_matches);
        }

        if (empty($receipt_matches[1])) {
            return array();
        }

        $receipt_numbers = array_values(array_unique($receipt_matches[1]));

        preg_match_all('/Total\s*Amount/', $content, $ta_matches, PREG_OFFSET_CAPTURE);

        $amounts = array();
        foreach ($ta_matches[0] as $match) {
            $offset = $match[1];
            $after = substr($content, $offset, 200);
            if (preg_match('/([\d,]+\.\d{2})/', $after, $amt_match)) {
                $amounts[] = str_replace(',', '', $amt_match[1]);
            }
        }

        if (empty($amounts)) {
            preg_match_all('/Net payable amount/', $content, $np_matches, PREG_OFFSET_CAPTURE);
            foreach ($np_matches[0] as $match) {
                $offset = $match[1];
                $after = substr($content, $offset, 200);
                if (preg_match('/([\d,]+\.\d{2})/', $after, $amt_match)) {
                    $amounts[] = str_replace(',', '', $amt_match[1]);
                }
            }
        }

        foreach ($receipt_numbers as $index => $receipt_number) {
            $results[] = array(
                'receipt_number' => $receipt_number,
                'total_amount' => isset($amounts[$index]) ? $amounts[$index] : ''
            );
        }

        return $results;
    }

    private function parse_with_pdftotext($file_path) {
        $pdftotext = 'C:\\inetpub\\xpdf-tools\\xpdf-tools-win-4.06\\bin64\\pdftotext.exe';

        if (!file_exists($pdftotext)) {
            return array();
        }

        $output_file = $file_path . '.txt';
        $cmd = '"' . $pdftotext . '" -layout "' . $file_path . '" "' . $output_file . '" 2>&1';
        $shell_output = shell_exec($cmd);

        if (!file_exists($output_file)) {
            $cmd2 = $pdftotext . ' -layout "' . $file_path . '" "' . $output_file . '" 2>&1';
            $shell_output = shell_exec($cmd2);
        }

        if (!file_exists($output_file)) {
            return array();
        }

        $full_text = file_get_contents($output_file);
        @unlink($output_file);

        if (empty(trim($full_text))) {
            return array();
        }

        $pages = preg_split('/\f/', $full_text);
        $results = array();

        foreach ($pages as $text) {
            if (empty(trim($text))) continue;

            $receipt_number = '';
            $total_amount = '';

            if (preg_match('/(TTSTHAC\d+)/i', $text, $matches)) {
                $receipt_number = trim($matches[1]);
            } elseif (preg_match('/(TTS[A-Z]{2,4}\d{10,})/i', $text, $matches)) {
                $receipt_number = trim($matches[1]);
            }

            $amount_patterns = array(
                '/Total\s*Amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
                '/Net\s*payable\s*amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
            );

            foreach ($amount_patterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $val = str_replace(',', '', trim($matches[1]));
                    if (floatval($val) > 0) {
                        $total_amount = $val;
                        break;
                    }
                }
            }

            if (!empty($receipt_number)) {
                $results[] = array(
                    'receipt_number' => $receipt_number,
                    'total_amount' => $total_amount
                );
            }
        }

        return $results;
    }

    private function parse_with_library($file_path) {
        $parser = new Parser();
        $pdf = $parser->parseFile($file_path);
        $pages = $pdf->getPages();
        $results = array();

        foreach ($pages as $page) {
            $text = $page->getText();

            if (empty(trim($text))) {
                try {
                    $dataTm = $page->getDataTm();
                    if (!empty($dataTm)) {
                        $text = '';
                        foreach ($dataTm as $item) {
                            if (isset($item[1])) $text .= $item[1] . ' ';
                        }
                    }
                } catch (\Exception $e) {}
            }

            if (empty(trim($text))) {
                try {
                    $textArray = $page->getTextArray();
                    if (!empty($textArray)) {
                        $text = implode(' ', $textArray);
                    }
                } catch (\Exception $e) {}
            }

            $receipt_number = '';
            $total_amount = '';

            if (preg_match('/(TTSTHAC\d+)/i', $text, $matches)) {
                $receipt_number = trim($matches[1]);
            } elseif (preg_match('/(TTS[A-Z]{2,4}\d{10,})/i', $text, $matches)) {
                $receipt_number = trim($matches[1]);
            }

            $amount_patterns = array(
                '/Total\s*Amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
                '/Net\s*payable\s*amount[\s\S]{0,80}?([\d,]+\.\d{2})/i',
            );

            foreach ($amount_patterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $val = str_replace(',', '', trim($matches[1]));
                    if (floatval($val) > 0) {
                        $total_amount = $val;
                        break;
                    }
                }
            }

            if (!empty($receipt_number)) {
                $results[] = array(
                    'receipt_number' => $receipt_number,
                    'total_amount' => $total_amount
                );
            }
        }

        return $results;
    }

    public function debug_pdf() {
        $data = array();
        $arr_css = array();
        $arr_js = array();
        $arr_input = array('title' => 'Debug PDF');

        $this->view_util->load_view_main('managepdf/debug_pdf_form', $data, $arr_css, $arr_js, $arr_input);
    }

    public function debug_pdf_process() {
        $config['upload_path'] = 'C:/inetpub/storage/bnyfoodproducts/uploads/xls/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 50000;
        $config['file_name'] = 'debug_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('pdf_file')) {
            echo '<pre>Upload Error: ' . $this->upload->display_errors('', '') . '</pre>';
            return;
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];

        $parser = new Parser();
        $pdf = $parser->parseFile($file_path);
        $pages = $pdf->getPages();

        echo '<div style="padding:20px;font-family:monospace;">';
        echo "<h2>Debug PDF - Total Pages: " . count($pages) . "</h2>";

        $i = 1;
        foreach ($pages as $page) {
            echo "<hr><h3>Page $i</h3>";

            echo "<h4>Method 1: getText()</h4><pre style='background:#f5f5f5;padding:10px;white-space:pre-wrap;'>";
            $text1 = $page->getText();
            echo htmlspecialchars($text1 ?: '(empty)');
            echo "</pre>";

            echo "<h4>Method 2: getDataTm()</h4><pre style='background:#f5f5f5;padding:10px;white-space:pre-wrap;'>";
            try {
                $dataTm = $page->getDataTm();
                if (!empty($dataTm)) {
                    foreach ($dataTm as $item) {
                        if (isset($item[1])) echo htmlspecialchars($item[1]) . " | ";
                    }
                } else {
                    echo "(empty)";
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            echo "</pre>";

            echo "<h4>Method 3: getTextArray()</h4><pre style='background:#f5f5f5;padding:10px;white-space:pre-wrap;'>";
            try {
                $textArray = $page->getTextArray();
                if (!empty($textArray)) {
                    echo htmlspecialchars(implode(' | ', array_filter($textArray)));
                } else {
                    echo "(empty)";
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            echo "</pre>";

            echo "<h4>extract_page_text() result:</h4><pre style='background:#eef;padding:10px;white-space:pre-wrap;'>";
            echo htmlspecialchars($this->extract_page_text($page) ?: '(empty)');
            echo "</pre>";

            if ($i >= 2) break;
            $i++;
        }
        echo '</div>';

        @unlink($file_path);
    }

    private function generate_excel($data) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $objPHPExcel = new PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Receipts');

        $sheet->SetCellValue('A1', 'No.');
        $sheet->SetCellValue('B1', 'Receipt Number');
        $sheet->SetCellValue('C1', 'Total Amount');

        $headerStyle = array(
            'font' => array('bold' => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D9E1F2')
            )
        );
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($data as $index => $item) {
            $sheet->SetCellValue('A' . $row, $index + 1);
            $sheet->setCellValueExplicit('B' . $row, $item['receipt_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->SetCellValue('C' . $row, floatval($item['total_amount']));
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);

        if ($row > 2) {
            $sheet->getStyle('C2:C' . ($row - 1))->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        $filename = "receipts_" . date("Y-m-d_H-i-s") . ".xlsx";
        $temp_file = 'C:/inetpub/storage/bnyfoodproducts/uploads/xls/' . $filename;

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($temp_file);

        if (!file_exists($temp_file) || filesize($temp_file) < 100) {
            @unlink($temp_file);
            $this->session->set_flashdata('error', 'สร้างไฟล์ Excel ไม่สำเร็จ กรุณาลองใหม่');
            redirect('managepdf');
            return;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Content-Length: ' . filesize($temp_file));
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        readfile($temp_file);
        @unlink($temp_file);
        exit;
    }
}
