<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material_unit extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');

		$this->auth_bl->check_session_exists();

     }
     
	public function material_unit_list()
	{
		$add_alt = $this->session->flashdata('add_material_unit');
		$edit_alt = $this->session->flashdata('edit_material_unit');

		$data_search = array(
			'material_unit_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 0,
			'per_page' => 5
		);

		$arr_units = $this->curl_bl->CallApi('POST','manufacture/material_unit/material_unit_search',$data_search);

		$data = array(
			'arr_units' => $arr_units['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Material Unit"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/manufacture/material_unit_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);

    	$arr_css = array(
			'site_new' => base_url()."assets/css/site_new.css"
		);

		$this->view_util->load_view_main('manufacture/material_unit/material_unit_list',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	} 

	public function material_unit_list_search()
	{
		$add_alt = $this->session->flashdata('add_material_unit');
		$edit_alt = $this->session->flashdata('edit_material_unit');
		$material_unit_search = $this->input->post('material_unit_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'material_unit_search' => $material_unit_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5
		);

		$arr_units = $this->curl_bl->CallApi('POST','manufacture/material_unit/material_unit_search',$data_search);

		$data = array(
			'arr_units' => $arr_units['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Material Unit"
		);

		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/manufacture/material_unit_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);

    	$arr_css = array(
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$this->view_util->load_view_main('manufacture/material_unit/material_unit_list',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function loaddata_more_ajax(){

		$material_unit_search = $this->input->post('material_unit_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'material_unit_search' => $material_unit_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5
		);

		$arr_units = $this->curl_bl->CallApiNospi('POST','manufacture/material_unit/material_unit_search',$data);

		$arr_data = array(
			'list_data' => $arr_units['Data']
		);
		echo json_encode($arr_data);

	}

	function add_material_unit_form(){

		$arr_input = array(
			'title' => "Material Unit"
		);

		$this->view_util->load_view_main('manufacture/material_unit/add_material_unit_form',NULL,NULL,NULL,$arr_input,MENU_CONFIG_USER);
	}

	function material_unit_add(){

		$material_unit = $this->input->post('material_unit');

		$data_curl = array(
			'material_unit' => $material_unit
		);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material_unit/material_unit_add',$data_curl);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_material_unit','success');
		}else{
			$this->session->set_flashdata('add_material_unit','fail');
		}

		redirect(base_url().'manufacture/material_unit/material_unit_list','refresh');
	}

	function edit_material_unit_form(){

		$id = $this->uri->segment(4);

		$arr_unit = $this->curl_bl->CallApi('GET','manufacture/material_unit/get_by_id/'.$id);

		$arr_input = array(
			'title' => "Material Unit"
		);

    	$data = array(
			'arr_unit' => $arr_unit['Data'],
			'id_en' => $id
		);
		
		$this->view_util->load_view_main('manufacture/material_unit/edit_material_unit_form',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);
	}

	function material_unit_edit(){

		$id_en = $this->input->post('id_en');
		$material_unit = $this->input->post('material_unit');

		$data_curl = array(
			'id_en' => $id_en,
			'material_unit' => $material_unit
		);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material_unit/material_unit_edit',$data_curl);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_material_unit','success');
		}else{
			$this->session->set_flashdata('edit_material_unit','fail');
		}

		redirect(base_url().'manufacture/material_unit/material_unit_list','refresh');
	}
}
