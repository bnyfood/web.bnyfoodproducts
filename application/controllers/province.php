<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Province extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]

		$this->load->library('businesslogic/selectbox_bl');

     }

	function get_districts(){

		$province_id = $this->input->post('province_id');
		$data_sel = $this->curl_bl->call_curl('GET','provinces/get_aumper/'.$province_id);

		//print_r($data_sel);
		$data = $this->selectbox_bl->make_list_district($data_sel['Data']);
		$arr_data = array(
			'districts' => $data
		);
		echo json_encode($arr_data);
	}
	
	function get_subdistricts(){
		$district_id = $this->input->post('district_id');
		$data_sel = $this->curl_bl->call_curl('GET','provinces/get_districts/'.$district_id);

		//print_r($arr_user);
		$data = $this->selectbox_bl->make_list_subdistrict($data_sel['Data']);
		$arr_data = array(
			'subdistricts' => $data
		);
		echo json_encode($arr_data);
	}
	
	function get_zipcode(){
		$subdistrict_id = $this->input->post('subdistrict_id');
		$data = $this->curl_bl->call_curl('GET','provinces/get_zipcode/'.$subdistrict_id);

		//print_r($arr_user);
		
		echo json_encode($data['Data']);
	}

	function get_by_zip(){

		$zip_txt = $this->input->post('zip_txt');
		$data_sel = $this->curl_bl->call_curl('GET','provinces/get_by_zip/'.$zip_txt);

		$arr_data = array(
			'province' => $data_sel['Data']
		);
		echo json_encode($arr_data);
	}
    
}
/* End of file users.php */
/* Location: ./application/controllers/users.php */
