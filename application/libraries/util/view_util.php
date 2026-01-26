<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class View_util 
{
	//Declare css and js variable.
	private $arr_css;
	private $arr_js;

	private $CI;
	
	
	function __construct() 
	{
				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		$this->CI->load->model('menu_model');
		$this->CI->load->model('sub_menu_model');

		//$this->CI->load->library('businesslogic/permission_bl');

    }

	public function load_view_main_v1($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null)
	{
		//$this->CI->benchmark->mark('code_start');
		//$this->CI->permission_bl->check_permission($menu_id_ref);
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		//$data["menu_id_ref"]=$menu_id_ref;

		$arr_page_menus = '';
		//$usergroup_id = $this->CI->session->userdata('usergroup_id');


		//$arr_page_menus = $this->CI->menu_model->get_menu_by_group_id($usergroup_id);

		

		/*$data["arr_page_menus"]=$arr_page_menus['Data'];

		$arr_page_submenus = '';

		$arr_page_submenus = $this->CI->sub_menu_model->get_submenu_by_group_id($usergroup_id);

		$data["arr_page_submenus"]=$arr_page_submenus['Data'];*/


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/navbar');
		$this->CI->load->view('template/main/menuleft');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/main/modal');	
		$this->CI->load->view('template/main/footer');	
		//$this->CI->benchmark->mark('code_end');
		//echo $this->CI->benchmark->elapsed_time('code_start', 'code_end');

		//echo $this->CI->benchmark->memory_usage();
	}

	public function load_view_main($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null)
	{
		//$arr_multi_group = $this->CI->session->userdata('multigroup');

		//print_r($arr_multi_group);
		//$this->CI->benchmark->mark('code_start');
		//$this->CI->permission_bl->check_permission($menu_id_ref);
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		$data["menu_id_ref"]=$menu_id_ref;

		$arr_page_menus = '';
		$usergroup_id = $this->CI->session->userdata(SESSION_PREFIX.'usergroup_id');
		$user_id = $this->CI->session->userdata(SESSION_PREFIX.'user_id');

		//echo ">>>>>".$user_id;


		$arr_page_menus = $this->CI->menu_model->get_menu_by_group_id($user_id);

		//print_r($arr_page_menus);

		$data["arr_page_menus"]=$arr_page_menus['Data'];

		$arr_page_submenus = '';

		$arr_page_submenus = $this->CI->sub_menu_model->get_submenu_by_group_id($usergroup_id);

		//print_r($arr_page_submenus);

		$data["arr_page_submenus"]=$arr_page_submenus['Data'];


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/menu_left');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/main/modal');
		$this->CI->load->view('template/main/footer');	
		//$this->CI->benchmark->mark('code_end');
		//echo $this->CI->benchmark->elapsed_time('code_start', 'code_end');

		//echo $this->CI->benchmark->memory_usage();
	}

	public function load_view_main_modal($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_html = array(),$arr_input=NULL,$menu_id_ref=null)
	{
		//$arr_multi_group = $this->CI->session->userdata('multigroup');

		//print_r($arr_multi_group);
		//$this->CI->benchmark->mark('code_start');
		//$this->CI->permission_bl->check_permission($menu_id_ref);
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_html"] = $arr_html;
		$data["arr_input"] = $arr_input;
		$data["menu_id_ref"]=$menu_id_ref;

		$arr_page_menus = '';
		$usergroup_id = $this->CI->session->userdata(SESSION_PREFIX.'usergroup_id');
		$user_id = $this->CI->session->userdata(SESSION_PREFIX.'user_id');

		//echo ">>>>>".$user_id;


		$arr_page_menus = $this->CI->menu_model->get_menu_by_group_id($user_id);

		//print_r($arr_page_menus);

		

		$data["arr_page_menus"]=$arr_page_menus['Data'];

		$arr_page_submenus = '';

		$arr_page_submenus = $this->CI->sub_menu_model->get_submenu_by_group_id($usergroup_id);

		//print_r($arr_page_submenus['Data']);

		$data["arr_page_submenus"]=$arr_page_submenus['Data'];


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/menu_left');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/main/modal');
		$this->CI->load->view('template/main/footer');	
		//$this->CI->benchmark->mark('code_end');
		//echo $this->CI->benchmark->elapsed_time('code_start', 'code_end');

		//echo $this->CI->benchmark->memory_usage();
	}

	public function load_view_main_v2($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null)
	{
		//$arr_multi_group = $this->CI->session->userdata('multigroup');

		//print_r($arr_multi_group);
		//$this->CI->benchmark->mark('code_start');
		//$this->CI->permission_bl->check_permission($menu_id_ref);
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		$data["menu_id_ref"]=$menu_id_ref;

		$arr_page_menus = '';
		$usergroup_id = $this->CI->session->userdata(SESSION_PREFIX.'usergroup_id');
		$user_id = $this->CI->session->userdata(SESSION_PREFIX.'user_id');

		//echo ">>>>>".$user_id;


		$arr_page_menus = $this->CI->menu_model->get_menu_by_group_id($user_id);

		//print_r($arr_page_menus);

		

		$data["arr_page_menus"]=$arr_page_menus['Data'];

		$arr_page_submenus = '';

		$arr_page_submenus = $this->CI->sub_menu_model->get_submenu_by_group_id($usergroup_id);

		$data["arr_page_submenus"]=$arr_page_submenus['Data'];


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/navbar');
		$this->CI->load->view('template/main/menuleft');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/main/modal');	
		$this->CI->load->view('template/main/footer');	
		//$this->CI->benchmark->mark('code_end');
		//echo $this->CI->benchmark->elapsed_time('code_start', 'code_end');

		//echo $this->CI->benchmark->memory_usage();
	}

	public function load_view_blankpage($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;

		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/blankpage/header',$data); //template/header.php
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/blankpage/footer');	
		
	}

	//main view v2
	public function load_mainview($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null,$body=null)
	{
		$this->CI->permission_bl->check_permission($menu_id_ref);
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		$data["menu_id_ref"]=$menu_id_ref;
		$data["header_body"]=$body;

		$arr_page_menus = '';
		$usergroup_id = $this->CI->session->userdata('usergroup_id');
		$data_menus = $this->CI->curl_bl->CallApi('GET','menu/get_menu/'.$usergroup_id);
		if($data_menus['Status'] == "Success"){
			//print_r($data_menus['Data']);
			$arr_page_menus = $data_menus['Data'];
		}

		$data["arr_page_menus"]=$arr_page_menus;

		$data_submenu_select = $this->CI->curl_bl->CallApi('GET','menu/get_submenu/'.$usergroup_id);
		if($data_submenu_select['Status'] == "Success"){
			//print_r($data_submenu_select['Data']);
			$arr_page_submenus = $data_submenu_select['Data'];
		}
		$data["arr_page_submenus"]=$arr_page_submenus;


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/mainview/header',$data); //template/header.php
		$this->CI->load->view('template/mainview/navbar');
		$this->CI->load->view('template/mainview/menuleft');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/mainview/modal');	
		$this->CI->load->view('template/mainview/footer');	
		
	}

	public function load_view_permission($main_page)
	{

		$this->CI->load->view('template/pageerror/header'); //template/header.php
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/pageerror/footer');	
		
	}

	public function load_view_popup($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$menu_id_ref=null)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		$data["menu_id_ref"]=$menu_id_ref;
		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/popup/header',$data); //template/header.php
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/popup/footer');	
		
	}

	public function load_view_login($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		
		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/

			
		//:[Load View Template]
		$this->CI->load->view('template/login/header',$data); //template/header.php
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/login/footer');	
		
	}

	public function load_view_register($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		
		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/

			
		//:[Load View Template]
		$this->CI->load->view('template/register/header',$data); //template/header.php
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/register/footer');	
		
	}
	
	
	public function page_not_found() //test 
	{
		redirect('http://gzdev/pagenotfound.asp');
	}
	
	public function page_main_admin() //test
	{
		redirect('http://gzdev/gazadmin/');
	}
	
	public function main_admin()
	{
	    if($_SERVER['HTTP_HOST']=="dev.phuketgazette.net")
		{
			redirect('http://gzdev/gazadmin/');	
		}
		else
		{
			redirect('http://'.$_SERVER['HTTP_HOST'].'/gazadmin/');	
		}
		
	}
	
	public function page_not_found_phuketgazette() //test 
	{
		redirect('http://'.$_SERVER['HTTP_HOST'].'/pagenotfound.asp');
	}
	
	
	
}

/* End of file view_util.php */
/* Location: ./application/libraries/util/view_util.php */