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

	//print_r($arr_page_menus['Data']['data_menus']);

		$data["arr_page_menus"]=$arr_page_menus['Data']['data_menus'];
		$data["arr_page_menus"] = $this->inject_alerts_menu($data["arr_page_menus"]);
		$data["ws_breadcrumb"] = menu_trail(isset($data["arr_page_menus"]) && is_array($data["arr_page_menus"]) ? $data["arr_page_menus"] : array(), $menu_id_ref);
		$data["ws_chrome"] = 1;

		/*$arr_page_submenus = '';

		$arr_page_submenus = $this->CI->sub_menu_model->get_submenu_by_group_id($usergroup_id);

		//print_r($arr_page_submenus);

		$data["arr_page_submenus"]=$arr_page_submenus['Data'];
		*/


		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
	
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/menu_left',$data);
		$this->CI->load->view('template/main/workspace',$data);
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
		$this->CI->load->view('template/main/menu_left',$data);
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

	public function social_login($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL)
	{
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;

		$this->CI->load->view('template/social_login/header',$data);
		$this->CI->load->view($main_page,$data);
		$this->CI->load->view('template/social_login/footer',$data);
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

	/**
	 * Subtle grey debug line for screenshots / AI readback.
	 * Keys: controller, method, uri segments, GET, plus any $extra.
	 */
	function build_ai_debug($extra = array())
	{
		$router = isset($this->CI->router) ? $this->CI->router : null;
		$ctrl = $router ? (string)$router->fetch_class() : '';
		$method = $router ? (string)$router->fetch_method() : '';
		$dir = $router ? (string)$router->fetch_directory() : '';
		$segments = array();
		if (isset($this->CI->uri) && is_object($this->CI->uri)) {
			$raw = $this->CI->uri->segment_array();
			if (is_array($raw)) {
				foreach ($raw as $i => $seg) {
					$segments['seg'.$i] = $seg;
				}
			}
		}
		$get = array();
		if (!empty($_GET) && is_array($_GET)) {
			foreach ($_GET as $k => $v) {
				if (is_scalar($v)) {
					$get['get.'.$k] = (string)$v;
				}
			}
		}
		$parts = array(
			'dir' => rtrim(str_replace('\\', '/', $dir), '/'),
			'controller' => $ctrl,
			'method' => $method,
			'url' => isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : ''
		);
		$parts = array_merge($parts, $segments, $get);
		if (is_array($extra)) {
			foreach ($extra as $k => $v) {
				if (is_scalar($v) || $v === null) {
					$parts[(string)$k] = ($v === null) ? '' : (string)$v;
				}
			}
		}
		return $parts;
	}

	function ai_debug_key_short($k)
	{
		$map = array(
			'dir' => 'd',
			'controller' => 'c',
			'method' => 'm',
			'url' => 'u',
			'view' => 'v',
			'menu_id' => 'mid',
			'platform' => 'p',
			'StartDate' => 'sd',
			'EndDate' => 'ed',
			'month_start' => 'ms',
			'month_end' => 'me',
			'rows' => 'n',
			'source' => 'src'
		);
		$k = (string)$k;
		if (isset($map[$k])) {
			return $map[$k];
		}
		if (preg_match('/^seg(\d+)$/', $k, $m)) {
			return 's'.$m[1];
		}
		if (strpos($k, 'get.') === 0) {
			return 'g.'.substr($k, 4);
		}
		return $k;
	}

	function ai_debug_html($parts = null)
	{
		if ($parts === null) {
			$parts = $this->build_ai_debug();
		}
		if (empty($parts) || !is_array($parts)) {
			return '';
		}
		$bits = array();
		foreach ($parts as $k => $v) {
			$v = trim((string)$v);
			if ($v === '') {
				continue;
			}
			$sk = $this->ai_debug_key_short($k);
			$bits[] = htmlspecialchars($sk, ENT_QUOTES, 'UTF-8')
				.'='
				.htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
		}
		if (empty($bits)) {
			return '';
		}
		$body = htmlspecialchars(implode(' · ', $bits), ENT_QUOTES, 'UTF-8');
		return '<div class="bny-ai-debug-wrap no-print" data-bny-ai-debug-wrap="1">'
			.'<label class="bny-ai-debug-tog" title="AI debug">'
			.'<input type="checkbox" class="bny-ai-debug-chk" autocomplete="off"> dbg'
			.'</label>'
			.'<div class="bny-ai-debug" data-bny-ai-debug="1" hidden>'.$body.'</div>'
			.'</div>';
	}

	function inject_alerts_menu($menus)
	{
		if (!is_array($menus) || $menus === array()) {
			return $menus;
		}
		foreach ($menus as $m) {
			if (is_array($m) && isset($m['menu_id']) && (int)$m['menu_id'] === (int)MENU_ALERTS) {
				return $menus;
			}
		}
		$item = array(
			'menu_id' => MENU_ALERTS,
			'menu_name' => 'อะเลิร์ท',
			'menu_name_en' => 'Alerts',
			'icon' => 'fas fa-bell',
			'link' => 'alerts',
			'submenus' => array(
				array(
					'menu_id' => MENU_ALERTS_TOKEN,
					'menu_name' => 'Token expire',
					'menu_name_en' => 'Token expire',
					'link' => 'alerts/token_expire',
					'lv3_submenus' => array()
				),
				array(
					'menu_id' => MENU_ALERTS_STATUS,
					'menu_name' => 'Status change',
					'menu_name_en' => 'Status change',
					'link' => 'alerts/status_change',
					'lv3_submenus' => array()
				)
			)
		);
		$out = array();
		$done = false;
		foreach ($menus as $m) {
			if (!is_array($m)) {
				continue;
			}
			$out[] = $m;
			$mid = isset($m['menu_id']) ? (int)$m['menu_id'] : 0;
			$name = isset($m['menu_name']) ? (string)$m['menu_name'] : '';
			$is_dash = ($mid === (int)MENU_DASHBOARD) || (stripos($name, 'แดช') !== false) || (strcasecmp($name, 'Dashboard') === 0);
			if (!$done && $is_dash) {
				$out[] = $item;
				$done = true;
			}
		}
		if (!$done) {
			array_unshift($out, $item);
		}
		return $out;
	}
}

/* End of file view_util.php */
/* Location: ./application/libraries/util/view_util.php */