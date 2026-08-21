<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Module_set extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
        $this->load->library('util/Random_util');
        $this->load->library('util/Get_address_util');

		$this->load->library('businesslogic/curl_bl');

		$this->load->model('bny_module_model');
        $this->load->model('bny_module_set_model');
        $this->load->model('bny_customer_activate_model');
        $this->load->model('bny_customer_activated_model');
        $this->load->model('bny_module_map_model');

        //$this->auth_bl->check_session_exists();
     }
     
     public function module_set_list()
     {
 
         $customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
         //echo $customer_code;

         $arr_module_sets = $this->bny_module_set_model->select_all();
         //print_r($arr_modules);
         if(!empty($arr_module_sets)){
             $max = sizeof($arr_module_sets);
 
             for($i=0;$i<$max;$i++){

                $bny_module_map = $this->bny_module_map_model->get_by_module_set_id($arr_module_sets[$i]['bny_module_set_id']);
                
                $arr_module_sets[$i]['bny_module_map'] = $bny_module_map;

                $arr_module_sets[$i]['bny_module_set_id'] = $this->encryption_util->encrypt_ssl($arr_module_sets[$i]['bny_module_set_id']);

             }
         }

         //print_r($arr_modules);
         
         $data = array(
             'arr_module_sets' => $arr_module_sets,
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
         
         $this->view_util->load_view_main('app_plan/module_set/module_set_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
 
     }

     function add_module_set_form(){

        $arr_modules = $this->bny_module_model->select_all();
        //print_r($arr_modules);
        if(!empty($arr_modules)){
            $max = sizeof($arr_modules);

            for($i=0;$i<$max;$i++){
                $arr_modules[$i]['bny_module_id'] = $this->encryption_util->encrypt_ssl($arr_modules[$i]['bny_module_id']);
            }
        }

		$arr_input = array(
			'title' => "App plan"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	//'config_usergroup' => base_url()."resources/js/validate/config_usergroup.js"
    	);	

        $data = array(
            'arr_modules' => $arr_modules
        );
		
		$this->view_util->load_view_main('app_plan/module_set/add_module_set_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

    function module_set_add(){

        $module_set_name = $this->input->post('module_set_name');
        $arr_module_id = $this->input->post('module_id');

        $activate_code = $this->random_util->create_activate_code(16);

        //print_r($arr_module_id);

        $data_insert = array(
            'module_set_name' => $module_set_name
        );

        $bny_module_set_id = $this->bny_module_set_model->insert($data_insert);

        $ip_addr = $this->input->ip_address();

        $mac = $this->get_address_util->get_macaddress();

        //echo "Max>>>>".$mac."<br>";

        $data_activate = array(
            'bny_module_set_id' => $bny_module_set_id,
            'activate_code' => $activate_code,
            'maxaddress' => $mac,
            'ipaddress'=> $ip_addr,
            'activate_status' => 1
        );

        $this->bny_customer_activate_model->insert($data_activate);

        $cnt_id = count($arr_module_id);

        for($i=0;$i<=$cnt_id-1;$i++){
            //echo $arr_module_id[$i]."<br>";
            $bny_module_id = $this->encryption_util->decrypt_ssl($arr_module_id[$i]);
            $data_map_insert = array(
                'bny_module_set_id' => $bny_module_set_id,
                'bny_module_id' => $bny_module_id
            );
            //print_r($data_map_insert);
            $this->bny_module_map_model->insert($data_map_insert);
        }

        redirect(base_url().'app_plan/module_set/module_set_list','refresh');

    }

    function del_action(){

		$id_en = $this->uri->segment(4);
        $bny_module_set_id = $this->encryption_util->decrypt_ssl($id_en);

        $this->bny_module_map_model->del_by_bny_module_set_id($bny_module_set_id);
        $this->bny_customer_activate_model->del_by_bny_module_set_id($bny_module_set_id);
        $this->bny_module_set_model->delete($bny_module_set_id);

		redirect(base_url().'app_plan/module_set/module_set_list','refresh');

	}
}