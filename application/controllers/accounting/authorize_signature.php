<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authorize_signature extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->model('web_authorize_signature_model');
		$this->auth_bl->check_session_exists();
	}

	public function authorize_signature_form()
	{
		$save_alt = $this->session->flashdata('save_authorize_signature');
		$row = $this->web_authorize_signature_model->get_active();
		$history = $this->web_authorize_signature_model->get_history();
		$signature_url = '';
		if (!empty($row) && !empty($row['file_name'])) {
			$signature_url = base_url().'uploads/authorize_signature/'.$row['file_name'];
		}

		$data = array(
			'save_alt' => $save_alt,
			'signature_row' => $row,
			'signature_url' => $signature_url,
			'signature_history' => $history
		);

		$arr_input = array(
			'title' => "Authorize signature"
		);

		$this->view_util->load_view_main('accounting/authorize_signature/authorize_signature_form', $data, NULL, NULL, $arr_input, MENU_ACCOUNT_AUTHORIZE_SIGNATURE);
	}

	public function authorize_signature_save()
	{
		$upload_dir = FCPATH.'uploads/authorize_signature/';
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0755, true);
		}

		$config = array(
			'upload_path' => $upload_dir,
			'allowed_types' => 'png',
			'max_size' => 2048,
			'encrypt_name' => true
		);
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('signature_file')) {
			$this->session->set_flashdata('save_authorize_signature', 'fail: '.$this->upload->display_errors('', ''));
			redirect('accounting/authorize_signature/authorize_signature_form');
			return;
		}

		$uploaded = $this->upload->data();
		$file_path = 'uploads/authorize_signature/'.$uploaded['file_name'];
		$save_mode = $this->input->post('save_mode');
		if ($save_mode === 'add') {
			$this->web_authorize_signature_model->save($uploaded['file_name'], $file_path);
		} else {
			$this->web_authorize_signature_model->update_latest($uploaded['file_name'], $file_path);
		}
		$this->session->set_flashdata('save_authorize_signature', 'success');
		redirect('accounting/authorize_signature/authorize_signature_form');
	}
}
