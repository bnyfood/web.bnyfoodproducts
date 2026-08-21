<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permission_bl{
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->helper('cookie');
		//$this->CI->load->library('util/encryption_util');
		$this->CI->load->library('util/cache_util');
		//$this->CI->load->library('businesslogic/curl_bl');
		$this->CI->load->library('businesslogic/data_bl');

		$this->CI->load->model('group_map_controller_model');
	}

	
	function check_permission($menu_id_ref){

		$is_cache = $this->CI->cache_util->chk_cache('businesslogic','Permission_bl','check_permission|'.$menu_id_ref);

		//echo "pre>>>>".$menu_id_ref;
		if(!$is_cache){	
			$usergroup_id = $this->CI->session->userdata('usergroup_id');
			$user_id = $this->CI->session->userdata('user_id');
			
			$data_curl = array(
				'usergroup_id' => $usergroup_id,
				'user_id' => $user_id,
				'menu_id' => $menu_id_ref
			);

			$data = $this->CI->curl_bl->CallApi('POST','menu/check_page_permission',$data_curl);
			$this->CI->cache->save('businesslogic','Permission_bl','check_permission|'.$menu_id_ref, $data, CAHCH_SEC);

			if(empty($data['Data'])){
				redirect(base_url().'users/permission','refresh');
			}
		}else{
			$data = $this->CI->cache->get('businesslogic','Permission_bl','check_permission|'.$menu_id_ref);
		}

		return $data;
	   
	}

	function check_allow_level($arr_user_level){

		$user_level = $this->CI->session->userdata('user_level');
			//echo "--user_level---->>".$user_level."<<--------";

		if (!in_array($user_level, $arr_user_level))
		  {
		  redirect(base_url().'users/permission','refresh');
		  }
	}

	function check_allow_controller($controller){

		$arr_multi_groups = $this->CI->session->userdata('multigroup');

		$data_levels = $this->CI->group_map_controller_model->get_by_controller($controller);

		$add = 0;
		$edit = 0;
		$del = 0;
		$read = 0;
		$arr_action = "";
		$is_allow = false;

		if(!empty($data_levels['Data'])){

			$arr_group_allow = $this->CI->data_bl->create_arr_id($data_levels['Data'],'group_id');
			
			foreach($arr_multi_groups as $multi_group){

				if (in_array($multi_group['group_id'], $arr_group_allow)){

					$is_allow = true;

					$arr_action = array(
						'is_add' => $multi_group['is_add'],
						'is_edit' => $multi_group['is_edit'],
						'is_del' => $multi_group['is_del'],
						'is_read' => $multi_group['is_read']
					);
					break;

					/*$add = $add +$multigroup['is_add'];
					$edit = $edit +$multi_group['is_edit'];
					$del = $del + $multi_group['is_del'];
					$read = $read + $multi_group['is_read'];*/

				}
			}

			if(!$is_allow){
				redirect(base_url().'users/permission','refresh');
			}

		}

		/*$arr_action = array(
			'is_add' => $add,
			'is_edit' => $edit,
			'is_del' => $del,
			'is_read' => $read
		);*/

		return $arr_action; 
	}

	function check_allow_controller_v1($controller){

		$data_curl = array(
			'controller' => $controller
		);

		$data_levels = $this->CI->curl_bl->CallApi('POST','menu/check_allow_controller',$data_curl);

		if(!empty($data_levels['Data'])){

			$arr_user_level = $this->CI->data_bl->create_arr_id($data_levels['Data'],'user_level');
		}else{
			$arr_user_level = array('1','2','3');
		}

		$user_level = $this->CI->session->userdata('user_level');
			//echo "--user_level---->>".$user_level."<<--------";

		if (!in_array($user_level, $arr_user_level))
		  {
		  	redirect(base_url().'users/permission','refresh');
		  }

		foreach ($data_levels['Data'] as $data_level){
			if($data_level['user_level'] == $user_level){
				$arr_per = array(
					'user_add' => $data_level['user_add'],
					'user_edit' => $data_level['user_edit'],
					'user_delete' => $data_level['user_delete']
				);
			}
		}  

		return $arr_per; 
	}
}	