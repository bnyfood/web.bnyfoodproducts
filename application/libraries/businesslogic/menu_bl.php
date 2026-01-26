<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_bl{
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->helper('cookie');
		$this->CI->load->library('util/encryption_util');

        $this->CI->load->model('menu_model');
	}

	
	function get_all_menu(){

        $data_menus = $this->CI->menu_model->get_by_parent('root');

        $cnt_mainmenu = count($data_menus);
        if($cnt_mainmenu > 0){
            for($i=0;$i<=$cnt_mainmenu-1;$i++){
                
                $arr_submenus = $this->CI->menu_model->get_by_parent($data_menus[$i]['menu_id']);
                if(!empty($arr_submenus)){

                    $cnt_submenu = count($arr_submenus);
                    for($j=0;$j<=$cnt_submenu-1;$j++){
                        $arr_submenus[$j]['menu_id'] = $this->CI->encryption_util->encrypt_ssl($arr_submenus[$j]['menu_id']);
                    }

                    $data_menus[$i]['submenus'] = $arr_submenus;
                }

                $data_menus[$i]['menu_id'] = $this->CI->encryption_util->encrypt_ssl($data_menus[$i]['menu_id']);
            }
        }
	    return $data_menus;
	}

    function get_menu_by_module_id($module_id){

        $data_menus = $this->CI->menu_model->get_by_parent_module_id('root',$module_id);

        $cnt_mainmenu = count($data_menus);
        if($cnt_mainmenu > 0){
            for($i=0;$i<=$cnt_mainmenu-1;$i++){
                
                $arr_submenus = $this->CI->menu_model->get_by_parent_module_id($data_menus[$i]['menu_id'],$module_id);
                if(!empty($arr_submenus)){

                    $cnt_submenu = count($arr_submenus);
                    for($j=0;$j<=$cnt_submenu-1;$j++){
                        $arr_submenus[$j]['menu_id'] = $this->CI->encryption_util->encrypt_ssl($arr_submenus[$j]['menu_id']);
                    }

                    $data_menus[$i]['submenus'] = $arr_submenus;
                }

                $data_menus[$i]['menu_id'] = $this->CI->encryption_util->encrypt_ssl($data_menus[$i]['menu_id']);
            }
        }
	    return $data_menus;

    }

}	