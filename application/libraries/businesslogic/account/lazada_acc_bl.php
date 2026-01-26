<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Lazada_acc_bl 
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();
		$this->CI->load->model('lazada_orders_model');
    $this->CI->load->model('lazada_orders_store_model');
		
    }

    public function getinvoice($arr_search)
  {

      $StartDateSearch = "";
      $EndDateSearch = "";

      if($arr_search['daterange'] != ""){      
        $date_arr=explode(" - ",$arr_search['daterange']);
        $StartDate=explode("/",$date_arr[0]);
        $EndDate=explode("/",$date_arr[1]);
        $StartDateSearch = $StartDate[2]."-".$StartDate[0]."-".$StartDate[1];
        $EndDateSearch = $EndDate[2]."-".$EndDate[0]."-".$EndDate[1];
      }

      //echo $StartDate[2]."-".$StartDate[0]."-".$StartDate[1];

      $page=1;

      //if($this->input->get('page')!==null)
      //{
        //  $page=$this->input->get('page');                                  
      //}  

      $arr=$this->CI->lazada_orders_model->getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($arr_search['taxinvoicetype'],$arr_search['ordernumber'],$arr_search['search_type'],$StartDateSearch,$EndDateSearch,$page,PAGINATION_SIZE);

    

    return $arr;

  }

  public function getinvoice_store($arr_search)
  {

    if($arr_search['ordernumber'] != "")
          {

          $arr=$this->CI->lazada_orders_store_model->getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($arr_search['taxinvoicetype'],$arr_search['ordernumber'],NULL,NULL,NULL,NULL);
          }
        else
          {
            
              $date_arr=explode(" - ",$arr_search['daterange']);
          $StartDate=explode("/",$date_arr[0]);
          $EndDate=explode("/",$date_arr[1]);

          //echo $StartDate[2]."-".$StartDate[0]."-".$StartDate[1];

          $page=1;

          //if($this->input->get('page')!==null)
          //{
            //  $page=$this->input->get('page');                                  
          //}  

          $arr=$this->CI->lazada_orders_store_model->getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($arr_search['taxinvoicetype'],NULL,$StartDate[2]."-".$StartDate[0]."-".$StartDate[1],$EndDate[2]."-".$EndDate[0]."-".$EndDate[1],$page,PAGINATION_SIZE);

        }

        return $arr;

  }
	
}