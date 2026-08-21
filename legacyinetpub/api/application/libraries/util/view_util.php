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
		//$this->CI->load->library('util/view/View_display');
		//$this->CI->load->library('business_logic/banner_bl');
		//$this->CI->load->library('business_logic/navigation_bl');
		//$this->CI->load->library('util/breadcrumb_component');
		//$this->CI->load->library('util/image_util');
    }
	
	
	
	
	
	
	
	public function loade_view($main_page,$data=NULL,$arr_css_add = array(),$arr_js_add = array(),$section = "index",$ref=0)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $this->arr_css;
		$data["arr_js"] = $this->arr_js;
		$data["section"]=$section;
		
		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view(HEADER_PANEL,$data); //template/header.php
		
		$this->load_menu_top_pattern_1($section,$ref);
		$this->CI->load->view($main_page);
		
		$this->load_menu_right_pattern_1($section);
		/*if($section == "classifieds")
			$this->load_menu_right_with_premium_ads($section);
		else
			$this->load_menu_right_pattern_1($section);
		*/	
		$this->CI->load->view(FOOTER_PANEL);	
		
	}

	public function load_view_main($main_page,$data=NULL,$arr_css = array(),$arr_js = array(),$arr_input=NULL,$section = "index",$ref=0)
	{
	
		//:[Put css and js array to $data array for display and running in view]			
		$data["arr_css"] = $arr_css;
		$data["arr_js"] = $arr_js;
		$data["arr_input"] = $arr_input;
		$data["section"]=$section;
		
		
		/*print_r($data["arr_js"]);
		print_r($data["arr_js_add"]);*/
		
		
			
		//:[Load View Template]
		$this->CI->load->view('template/main/header',$data); //template/header.php
		$this->CI->load->view('template/main/topbar');
		$this->CI->load->view('template/main/leftbar');
		$this->CI->load->view($main_page);
		$this->CI->load->view('template/main/footer');	
		
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