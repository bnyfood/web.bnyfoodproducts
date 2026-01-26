<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boxsize_bl{
	
	public function __construct(){
		$this->CI =& get_instance();

		$this->CI->load->model('bny_product_volume_model');
		$this->CI->load->model('bny_box_size_model');
	}

	public $arr_pro_final = array();
	
	function cal_box($arr_items){

		$sum_value = 0;

		$arr_products = $this->build_product_arr($arr_items);

		//print_r($arr_products);
		
		//$arr_products=array('2000','2000','2000','2000','2000','2000','1500','1500');

		//$arr_products=array('2000','1000');

		$arr_boxes = $this->build_box_arr();
		//print_r($arr_boxes);

		$this->push_in_box($arr_products,$arr_boxes);
		//print_r($this->arr_pro_final);
		return $this->arr_pro_final;

	}

	function build_box_arr(){
		$arr_box_id = array();
		$arr_boxes = $this->CI->bny_box_size_model->select_by_mearch(0);
		if(!empty($arr_boxes)){
			foreach($arr_boxes as $arr_box){

				if($arr_box['box_stock'] > 0){
					array_push($arr_box_id,$arr_box['box_size_id']);
				}else{
					if($arr_box['is_instead'] == 1){
						$arr_box_instead = $this->CI->bny_box_size_model->select_by_instead($arr_box['boxsize_v']);
						if(!empty($arr_box_instead)){
							array_push($arr_box_id,$arr_box_instead['box_size_id']);
						}
					}
				}
			}
		}

		$arr_boxes_new = $this->CI->bny_box_size_model->select_by_arr_id($arr_box_id);

		return $arr_boxes_new;
	}

	function build_product_arr($arr_items){

		$arr_products =  array();

		foreach($arr_items as $arr_item){

			if($arr_item['product_num_type'] == 1 ){

				$arr_sku = $this->CI->bny_product_volume_model->select_by_name($arr_item['product_type1']);
				$num_qty = $arr_item['product_quality1'] * $arr_item['Qty'];

				for($i=1;$i<=$num_qty;$i++){
					array_push($arr_products,$arr_sku['bny_product_volume_v']);
				}


			}elseif($arr_item['product_num_type'] == 2 ){

				$arr_sku = $this->CI->bny_product_volume_model->select_by_name($arr_item['product_type1']);
				$num_qty = $arr_item['product_quality1'] * $arr_item['Qty'];

				for($i=1;$i<=$num_qty;$i++){
					array_push($arr_products,$arr_sku['bny_product_volume_v']);
				}

				$arr_sku2 = $this->CI->bny_product_volume_model->select_by_name($arr_item['product_type2']);
				$num_qty2 = $arr_item['product_quality2'] * $arr_item['Qty'];

				for($j=1;$j<=$num_qty2;$j++){
					array_push($arr_products,$arr_sku2['bny_product_volume_v']);
				}

			}
		}

		$sort_arr = $this->arr_sort_desc($arr_products);
		//print_r($sort_arr);

		return $sort_arr;

	}

	function build_product_arr_v1($arr_items){

		$arr_products =  array();

		foreach($arr_items as $arr_item){
			
			$is_sp = $this->chk_sku($arr_item['Sku']);
			echo $arr_item['Sku']."-".$is_sp."<br>";
			$sku_s = "";
			$sku_p = "";
			$s_qty=0;
			$p_qty=0;
			if($is_sp == 1){
				$ex_sku = explode("p",$arr_item['Sku']);
				$sku_s = $ex_sku[0];
				$sku_p = "p".$ex_sku[1];

				$arr_sku_s = $this->CI->bny_product_volume_model->select_by_name($sku_s);
				$arr_sku_p = $this->CI->bny_product_volume_model->select_by_name($sku_p);

				for($i=1;$i<=$arr_item['Qty'];$i++){
					array_push($arr_products,$arr_sku_s['bny_product_volume_v']);
					array_push($arr_products,$arr_sku_p['bny_product_volume_v']);
				}

			}elseif($is_sp == 2){

				$sku_s = $arr_item['Sku'];
				$arr_sku_s = $this->CI->bny_product_volume_model->select_by_name($sku_s);

				for($i=1;$i<=$arr_item['Qty'];$i++){
					array_push($arr_products,$arr_sku_s['bny_product_volume_v']);
				}

			}elseif($is_sp == 3){

				$sku_p = $arr_item['Sku'];
				$arr_sku_p = $this->CI->bny_product_volume_model->select_by_name($sku_p);
				
				for($i=1;$i<=$arr_item['Qty'];$i++){
					array_push($arr_products,$arr_sku_p['bny_product_volume_v']);
				}
			}

			//echo $sku_s."-".$sku_p."<br>";
		}
		$sort_arr = $this->arr_sort_desc($arr_products);
		//print_r($sort_arr);

		return $sort_arr;

	}

	function push_in_box($arr_products,$arr_boxes){

		$arr_box_tmp = array();
		
		//$arr_boxes = $this->CI->bny_box_size_model->select_box_asc();
		$cnt_box = count($arr_boxes);
		$last_box = $cnt_box-1;

		$cnt_pro = count($arr_products);
			$num_pro = 0;

			for ($j = 0; $j <= $cnt_pro-1; $j++) { //loop product
				//echo $arr_products[$j]."--";
				$num_pro = $num_pro+1;

				for ($i = 0; $i <= $cnt_box-1; $i++) { // loop box

					if($arr_boxes[$i]['boxsize_v'] >= $arr_products[$j]){

						$new_val = $arr_boxes[$i]['boxsize_v'] - $arr_products[$j];
						$arr_boxes[$i]['boxsize_v'] = $new_val;

						//array_push($arr_box_use, $arr_boxes[$i]['boxsize_name']);

						$arr_pro_use = array();
						for ($k = 0; $k <= $j; $k++) {
							array_push($arr_pro_use, $arr_products[$k]);
						}

						$arr_boxes[$i]['product_v']=$arr_pro_use;

					}else{

						//echo $arr_products[$j].">>".$i."<br>";
						if($i < $last_box){
							unset($arr_boxes[$i]);
						}else{//last box size
							array_push($arr_box_tmp, $arr_products[$j]);
						}
						
					}
				}

				$arr_boxes = array_values($arr_boxes);
				$cnt_box = count($arr_boxes);
				$last_box = $cnt_box-1;

			}

			//print_r($arr_boxes);
			//print_r($arr_box_tmp);

			array_push($this->arr_pro_final, $arr_boxes[0]);
			if(count($arr_box_tmp) > 0){
				$sort_arr = $this->arr_sort_desc($arr_box_tmp);
				$this->push_in_box($sort_arr);
			}

	}

	function push_in_box_v2($arr_products){

		$arr_box_tmp = array();
		
		$arr_boxes = $this->CI->bny_box_size_model->select_box_asc();
		$cnt_box = count($arr_boxes);
		$last_box = $cnt_box-1;

		$cnt_pro = count($arr_products);
			$num_pro = 0;

			for ($j = 0; $j <= $cnt_pro-1; $j++) { //loop product
				//echo $arr_products[$j]."--";
				$num_pro = $num_pro+1;

				for ($i = 0; $i <= $cnt_box-1; $i++) { // loop box

					if($arr_boxes[$i]['boxsize_v'] >= $arr_products[$j]){

						$new_val = $arr_boxes[$i]['boxsize_v'] - $arr_products[$j];
						$arr_boxes[$i]['boxsize_v'] = $new_val;

						//array_push($arr_box_use, $arr_boxes[$i]['boxsize_name']);

						$arr_pro_use = array();
						for ($k = 0; $k <= $j; $k++) {
							array_push($arr_pro_use, $arr_products[$k]);
						}

						$arr_boxes[$i]['product_v']=$arr_pro_use;

					}else{

						//echo $arr_products[$j].">>".$i."<br>";
						if($i < $last_box){
							unset($arr_boxes[$i]);
						}else{//last box size
							array_push($arr_box_tmp, $arr_products[$j]);
						}
						
					}
				}

				$arr_boxes = array_values($arr_boxes);
				$cnt_box = count($arr_boxes);
				$last_box = $cnt_box-1;

			}

			//print_r($arr_boxes);
			//print_r($arr_box_tmp);

			array_push($this->arr_pro_final, $arr_boxes[0]);
			if(count($arr_box_tmp) > 0){
				$sort_arr = $this->arr_sort_desc($arr_box_tmp);
				$this->push_in_box($sort_arr);
			}

	}

	function push_in_box_v1($arr_products){

		$arr_box_use = array(
			'box_point' => 0
		);
		$arr_pro_in_box = array();
		$arr_pro_tmp = array();

		$start_box = 0;

		$arr_boxes = $this->CI->bny_box_size_model->select_box_asc();

		$cnt_box = count($arr_boxes);
		$last_box = $cnt_box-1;
		//saprint_r($arr_boxes);

			$cnt_pro = count($arr_products);
			$num_pro = 0;

			for ($j = 0; $j < $cnt_pro-1; $j++) { //loop product
				$num_pro = $num_pro+1;

				for ($i = $start_box; $i < $cnt_box-1; $i++) { // loop box

					if($i < $last_box){ // Not last box

						echo "not last box <br>";

						if($arr_boxes[$i]['boxsize_v'] >= $arr_products[$j]){

							echo "NL pro can put box >> ".$i." pro >> ".$j."<br>";

							$new_val = $arr_boxes[$i]['boxsize_v'] - $arr_products[$j];
							$arr_boxes[$i]['boxsize_v'] = $new_val;

							$arr_box_use['box_point'] = $i;
							array_push($arr_pro_in_box, $arr_products[$j]);
							$arr_box_use['proucts'] = $arr_pro_in_box;

							$start_box = $i;

							print_r($arr_boxes);
							print_r($arr_box_use);

							break;
						}else{
							echo "NL pro can't put box >> ".$i." pro >> ".$j."<br>";
							$sum_pro_box = array_sum($arr_pro_in_box);
							$new_val = $arr_boxes[$i+1]['boxsize_v'] - $sum_pro_box;
							$arr_boxes[$i+1]['boxsize_v'] = $new_val;

							//set box to 
							$arr_box = $this->CI->bny_box_size_model->select_by_id($arr_boxes[$i]['box_size_id']);
							$arr_boxes[$i]['boxsize_v'] = $arr_box['boxsize_v'];

							//$arr_box_use['box_point'] = $i+1;
							////$start_box = $i+1;
							$j = $j;

							//print_r($arr_boxes);
							//print_r($arr_box_use);

						}
					}else{ // last box
						echo "last box box >> ".$i." pro >> ".$j."<br>";

						if($arr_boxes[$i]['boxsize_v'] >= $arr_products[$j]){
							echo "L pro can put <br>";

							$new_val = $arr_boxes[$i]['boxsize_v'] - $arr_products[$j];
							$arr_boxes[$i]['boxsize_v'] = $new_val;

							$arr_box_use['box_point'] = $i;
							array_push($arr_pro_in_box, $arr_products[$j]);
							$arr_box_use['proucts'] = $arr_pro_in_box;

							$start_box = $i;

							print_r($arr_boxes);
							print_r($arr_box_use);
							break;
						}else{
							echo "L pro can put  move to tmp box >> ".$i." pro >> ".$j."<br>";
							array_push($arr_pro_tmp, $arr_products[$j]);
						}

					}

				}//for ($i = $start_box; $i < $cnt_box-1; $i++) { // loop box

			}//for ($j = 0; $j < $cnt_pro-1; $j++) { //loop product

			array_push($this->arr_pro_final, $arr_box_use);
			
		
		$cnt_pro_tmp = count($arr_pro_tmp);
		if($cnt_pro_tmp > 0 ){
			$this->push_in_box($arr_pro_tmp);
		}

	}

	function arr_sort_desc($array){
		$count = count($array);

		//print_r($array);
		for ($i = 0; $i < $count; $i++) {
		    for ($j = $i + 1; $j < $count; $j++) {
		        if ($array[$i] < $array[$j]) {
		            $temp = $array[$i];
		            $array[$i] = $array[$j];
		            $array[$j] = $temp;
		        }
		    }
		}
		//echo "Sorted Array:" . "<br/>";
		//print_r($array);
		return $array;
	}

	function chk_sku($sku){

		$is_sp = 0;

		if(strpos($sku, 's') !== false){
		   // echo "Word Found!";
			if(strpos($sku, 'p') !== false){
		   // echo "Word Found!";
				$is_sp = 1;
			} else{
			    $is_sp = 2;
			}

		} else{
		    if(strpos($sku, 'p') !== false){
		   // echo "Word Found!";
				$is_sp = 3;
			} else{
			    $is_sp = 4;
			}
		}

		return $is_sp;

	}

	function cal_box_sumval($arr_items){

		$sum_value = 0;

		foreach($arr_items as $arr_item){


			$sum_s_p = 0;
			
			$is_sp = $this->chk_sku($arr_item['Sku']);
			echo $arr_item['Sku']."-".$is_sp."<br>";
			$sku_s = "";
			$sku_p = "";
			$s_qty=0;
			$p_qty=0;
			if($is_sp == 1){
				$ex_sku = explode("p",$arr_item['Sku']);
				$sku_s = $ex_sku[0];
				$sku_p = "p".$ex_sku[1];

				$arr_sku_s = $this->CI->bny_product_volume_model->select_by_name($sku_s);
				$arr_sku_p = $this->CI->bny_product_volume_model->select_by_name($sku_p);

				$s_qty = $arr_sku_s['bny_product_volume_v'] * $arr_item['Qty'];
				$p_qty = $arr_sku_p['bny_product_volume_v'] * $arr_item['Qty'];

				$sum_s_p = $s_qty + $p_qty;

				$sum_value = $sum_value + $sum_s_p;

			}elseif($is_sp == 2){

				$sku_s = $arr_item['Sku'];
				$arr_sku_s = $this->CI->bny_product_volume_model->select_by_name($sku_s);
				$s_qty = $arr_sku_s['bny_product_volume_v'] * $arr_item['Qty'];

				$sum_value = $sum_value + $s_qty;

			}elseif($is_sp == 3){

				$sku_p = $arr_item['Sku'];
				$arr_sku_p = $this->CI->bny_product_volume_model->select_by_name($sku_p);
				$p_qty = $arr_sku_p['bny_product_volume_v'] * $arr_item['Qty'];

				$sum_value = $sum_value + $p_qty;
			}

			//echo $sku_s."-".$sku_p."<br>";
			
		}

		echo $sum_value;

	}
	

}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */