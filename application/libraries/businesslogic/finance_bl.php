<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_bl{
	public function __construct(){
		$this->CI =& get_instance();

		$this->CI->load->model('lazada_finance_transaction_details_model');
	}
	
	function find_all_data($arr_ordernos){
	
		$arr_newdata = array();	

		$cnt_arr_tran = count($arr_ordernos);
		
		foreach($arr_ordernos as $arr_orderno){

			$arr_trans_by_ordernos = $this->CI->lazada_finance_transaction_details_model->get_by_orderno($arr_orderno['order_number']);

			array_push($arr_newdata,$arr_trans_by_ordernos);

		}
		
		return $arr_newdata;
	}

	function find_wrong_data($arr_ordernos){
	
		$arr_newdata = array();	

		$cnt_arr_tran = count($arr_ordernos);
		
		foreach($arr_ordernos as $arr_orderno){

			$arr_trans_by_ordernos = $this->CI->lazada_finance_transaction_details_model->get_by_orderno($arr_orderno['order_number']);

			$tran_cnt = count($arr_trans_by_ordernos);

			if($tran_cnt < 5){
				array_push($arr_newdata,$arr_trans_by_ordernos);
			}else{
				// check paid all status
				$paid = 0;
				foreach($arr_trans_by_ordernos as $arr_trans_by_orderno){
					if($arr_trans_by_orderno['paid_status'] == 'Paid'){
						$paid = 1;
						break;
					}
				}

				if($paid == 0){
					array_push($arr_newdata,$arr_trans_by_ordernos);
				}
			}

		}
		
		return $arr_newdata;
	}

	function find_true_data($arr_ordernos){
	
		$arr_newdata = array();	

		$cnt_arr_tran = count($arr_ordernos);
		
		foreach($arr_ordernos as $arr_orderno){

			$arr_trans_by_ordernos = $this->CI->lazada_finance_transaction_details_model->get_by_orderno($arr_orderno['order_number']);

			$tran_cnt = count($arr_trans_by_ordernos);

			if($tran_cnt > 4){
				
				// check paid all status
				$paid = 0;
				foreach($arr_trans_by_ordernos as $arr_trans_by_orderno){
					if($arr_trans_by_orderno['paid_status'] == 'Paid'){
						$paid = 1;
						break;
					}
				}

				if($paid == 1){
					array_push($arr_newdata,$arr_trans_by_ordernos);
				}
			}

		}
		
		return $arr_newdata;
	}
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */