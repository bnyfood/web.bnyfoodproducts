<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Data_prepare_bl
{

	
	function __construct() 
	{
		
			//echo '<br/>banner_bl constructor running <br/><br/>';
			
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();

		$this->CI->load->model('user_group_model');
		$this->CI->load->model('web_bny_customer_model');
		$this->CI->load->model('groupmapmenu_model');
		$this->CI->load->model('groupmapuser_model');
		$this->CI->load->model('groupuser_permission_model');
		$this->CI->load->model('group_map_controller_model');
		
    }
	


	function setdata_register($user_id,$shop_name,$customer_code)
	{
		$success = false;
			$group_id = $this->create_group($shop_name,$shop_id,$customer_code,$user_id);
			if(!empty($group_id)){
				$this->user_map_group($group_id,$user_id);
				//$this->group_map_user_permission($group_id,$user_id);
				$this->group_map_menu($group_id);
				
				$success = true;
			}
      return $success;
	}

	function create_shop($shop_name,$customer_code){

		$arr_shop = array(
			'ShopName' => $shop_name,
			'domain' => '',
			'URL_home' => '',
			'ip' => '',
			'customer_code' => $customer_code
		);

      $shop_id = $this->CI->web_shop_model->insert($arr_shop);

      return $shop_id;
	}

	function create_group($shop_name,$shop_id,$customer_code,$user_id){

		$group_name = "Admin ".$shop_name;

		$arr_data = array(
			'group_name' => $group_name,
			'ShopID' => $shop_id,
			'is_add' => 1,
			'is_edit' => 1,
			'is_del' => 1,
			'is_read' => 1,
			'customer_code' => $customer_code
		);

      $group_id = $this->CI->user_group_model->insert($arr_data);


      $data_group = array(
      	'usergroup_id' => $group_id
      );

      $this->CI->web_bny_customer_model->update($data_group,$user_id);

      return $group_id;

	}

	function user_map_group($group_id,$user_id){

		$data_map_group = array(
			'group_id' =>$group_id,
			'BNYCustomerID' => $user_id,
			'user_level_id' => 1,
			'is_main_group' => 1
		);

		$this->CI->groupmapuser_model->insert($data_map_group);

	}

	function group_map_user_permission($group_id,$user_id){
		//user_level_id 1=admin,2=super user,3=user
		$data = array(
			'group_id' =>$group_id,
			'BNYCustomerID' => $user_id,
			'user_level_id' => 1,
			'is_main_group' => 1
		);

		$id = $this->CI->groupmapuser_model->insert($data);

		$data_per = array(
			'groupmapuser_id' => $id,
			'is_add' => 1,
			'is_edit' => 1,
			'is_del' => 1,
			'is_read' => 1
		);

		$this->CI->groupuser_permission_model->insert($data_per);
	}

	function group_map_menu($group_id){

		$data = array(
		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_SETTING // การตั้งค่า
		   ),
		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_DASHBOARD // Dashboard
		   ),
		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_GROUP // กลุ่ม
		   ),
		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_EMPLOYEE // พนักงาน
		   ),
		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_SHOP // ร้านค้า
		   )
		);

		$this->CI->groupmapmenu_model->insert_batch($data);

	}

	function group_map_menu_temp($group_id){

		$data = array(

		   array(
		      'group_id' => $group_id ,
		      'menu_id' => MENU_ID_DASHBOARD // Dashboard
		   )
		);

		$this->CI->groupmapmenu_model->insert_batch($data);

	}

	
}

/* End of file article_bl.php */
/* Location: ./application/libraries/bussiness_logic/article_bl.php */