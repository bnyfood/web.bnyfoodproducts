<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Module extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
        $this->load->library('businesslogic/data_bl');
        $this->load->library('businesslogic/menu_bl');

		$this->load->model('bny_module_model');
        $this->load->model('menu_model');
        $this->load->model('bny_module_map_menu_model');

        //$this->auth_bl->check_session_exists();

     }
     
     public function module_list()
     {
 
         $customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
         //echo $customer_code;
 
         $arr_modules = $this->bny_module_model->select_all();
         //print_r($arr_modules);
         if(!empty($arr_modules)){
             $max = sizeof($arr_modules);
 
             for($i=0;$i<$max;$i++){

                $arr_menus = $this->menu_bl->get_menu_by_module_id($arr_modules[$i]['bny_module_id']);

                $arr_modules[$i]['arr_menus'] = $arr_menus;

                $arr_modules[$i]['bny_module_id'] = $this->encryption_util->encrypt_ssl($arr_modules[$i]['bny_module_id']);
             }
         }

         //print_r($arr_modules);
         
         $data = array(
             'arr_modules' => $arr_modules,
             'add_alt' => $add_alt,
             'edit_alt' => $edit_alt
         );
 
         //print_r($data);
         
         $arr_input = array(
             'title' => "App plan"
         );
         
         $arr_js = array(
             'usergroup_list' => base_url()."resources/js/config_system/usergroup/usergroup_list.js"
         );
         
         $this->view_util->load_view_main('app_plan/module/module_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
 
     }

    function add_module_form(){

        $arr_menus = $this->menu_bl->get_all_menu();
        //print_r($arr_menus);

        $arr_input = array(
            'title' => "App plan"
        );

        $arr_js = array(
            'validate' => base_url()."assets/js/jquery.validate.min.js",	
            //'config_usergroup' => base_url()."resources/js/validate/config_usergroup.js"
        );	

        $data = array(
            'arr_menus' => $arr_menus
        );

        $this->view_util->load_view_main('app_plan/module/add_module_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

    function module_add(){

        $module_name = $this->input->post('module_name');
        $arr_menu_id = $this->input->post('menu_id');

        $data_insert = array(
            'module_name' => $module_name
        );

        $bny_module_id =  $this->bny_module_model->insert($data_insert);

        $cnt_id = count($arr_menu_id);

        for($i=0;$i<=$cnt_id-1;$i++){
            //echo $arr_menu_id[$i]."<br>";
            $menu_id = $this->encryption_util->decrypt_ssl($arr_menu_id[$i]);
            $data_map_menu_insert = array(
                'bny_module_id' => $bny_module_id,
                'menu_id' => $menu_id
            );
            //print_r($data_map_insert);
            $this->bny_module_map_menu_model->insert($data_map_menu_insert);
        }

        redirect(base_url().'app_plan/module/module_list','refresh');
    }

    function edit_module_form(){

		$id_en = $this->uri->segment(4);
        $id = $this->encryption_util->decrypt_ssl($id_en);

		$arr_module = $this->bny_module_model->select_by_id($id);

		if(!empty($arr_module)){

			$arr_module['bny_module_id'] = $this->encryption_util->encrypt_ssl($arr_module['bny_module_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "App plan"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_usergroup_edit.js"
    	);

    	$data = array(
			'arr_module' => $arr_module,
            'id_en' => $id_en
		);
		
		$this->view_util->load_view_main('app_plan/module/edit_module_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

    function module_edit(){

        $id_en = $this->input->post('id_en');
        $id = $this->encryption_util->decrypt_ssl($id_en);

        $module_name = $this->input->post('module_name');

        $data_update = array(
            'module_name' => $module_name
        );

        $this->bny_module_model->update($data_update,$id);

        redirect(base_url().'app_plan/module/module_list','refresh');
    }

    function del_action(){

		$id_en = $this->uri->segment(4);
        $id = $this->encryption_util->decrypt_ssl($id_en);

		$this->bny_module_model->delete($id);
        $this->bny_module_map_menu_model->del_by_bny_module_id($id);

		redirect(base_url().'app_plan/module/module_list','refresh');

	}
}