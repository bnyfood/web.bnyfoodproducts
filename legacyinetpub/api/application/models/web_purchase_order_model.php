<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_purchase_order_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_purchase_order', $data);
    	$insert_id = $this->db->insert_id();
    	//echo $this->db->last_query();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('web_purchase_order_id',$id);
		$this->db->update('web_purchase_order',$data);
	}

	function delete($id){
		$this->db->where('web_purchase_order_id',$id);
		$this->db->delete('web_purchase_order');
	}

	function po_del_by_code($code){
		$this->db->where('pocode',$code);
		$this->db->delete('web_purchase_order');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_purchase_order');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_purchase_order');
		$this->db->where('web_purchase_order_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_purchase_order');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_supp_code($shop_id,$supp_id,$pocode){
		$this->db->select("*,FORMAT ( [cdate] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('web_purchase_order');
		$this->db->where('ShopID',$shop_id);
		$this->db->where('web_supplier_id',$supp_id);
		$this->db->where('pocode',$pocode);
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
	}

	function select_by_ShopID($shop_id){
		$this->db->select("*,FORMAT ( [cdate] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('web_purchase_order');
		$this->db->where('ShopID',$shop_id);
		$this->db->order_by('web_purchase_order_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
	}

	function select_by_code($shop_id,$pocode){
		$this->db->select('*,web_purchase_order.status as po_status');
		$this->db->from('web_purchase_order');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$this->db->where('web_purchase_order.pocode',$pocode);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_code_join($shop_id,$po_number,$web_supplier_id){
		$this->db->select("*,web_purchase_order.status as po_status,FORMAT ( web_purchase_order.cdate , 'dd/MM/yyyy' ) as po_cdate");
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material', 'web_purchase_order.web_purchase_order_id = web_purchase_material.web_purchase_order_id');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'web_purchase_material.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('material_map_supplier_history_lasted', 'web_purchase_material.web_material_id = material_map_supplier_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');

		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$this->db->where('web_purchase_order.po_number',$po_number);
		$this->db->where('material_map_supplier_history_lasted.web_supplier_id',$web_supplier_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
		//material_map_supplier_history_lasted
	}

	function select_by_code_join_v1($shop_id,$pocode){
		$this->db->select('*,web_purchase_order.status as po_status');
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material', 'web_purchase_order.web_purchase_order_id = web_purchase_material.web_purchase_order_id');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history', 'web_purchase_material.web_material_history_id = web_material_history.web_material_history_id');
		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$this->db->where('web_purchase_order.pocode',$pocode);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_ponumber_join($po_number){
		$this->db->select('*,web_purchase_order.status as po_status');
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material', 'web_purchase_order.web_purchase_order_id = web_purchase_material.web_purchase_order_id');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history', 'web_purchase_material.web_material_history_id = web_material_history.web_material_history_id');
		$this->db->where('web_purchase_order.po_number',$po_number);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_ponumber($shop_id,$po_number){
		$this->db->select('*');
		$this->db->from('web_purchase_order');
		$this->db->like('po_number',$po_number);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_ponumber_one($shop_id,$po_number){
		$this->db->select('*,web_purchase_order.cdate as po_date');
		$this->db->from('web_purchase_order');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('po_number',$po_number);
		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function last_order_code_by_yymm($yymm,$ShopID){
		$this->db->select('*');
		$this->db->from('web_purchase_order');
		$this->db->where("FORMAT ( [cdate] , 'yyyy-MM' ) = '".$yymm."'");
		$this->db->where('web_purchase_order.ShopID',$ShopID);
		$this->db->order_by('web_purchase_order_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_ponumber_join_reciept($shop_id,$po_number){
		$this->db->select('*,web_purchase_order.web_purchase_order_id as purchase_order_id,web_purchase_material_history.web_purchase_material_id as purchase_material_id');
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material_history', 'web_purchase_order.web_purchase_order_id = web_purchase_material_history.web_purchase_order_id');
		$this->db->join('web_material_history', 'web_purchase_material_history.web_material_history_id = web_material_history.web_material_history_id');
		$this->db->join('inventory_reciept_po', 'web_purchase_material_history.web_purchase_material_history_id = inventory_reciept_po.web_purchase_material_history_id','left outer');
		
		$this->db->where('web_purchase_order.po_number',$po_number);
		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_ponumber_join_reciept_location($shop_id,$po_number){
		$this->db->select('*,web_purchase_order.web_purchase_order_id as purchase_order_id,web_purchase_material_history.web_purchase_material_id as purchase_material_id');
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material_history', 'web_purchase_order.web_purchase_order_id = web_purchase_material_history.web_purchase_order_id');
		$this->db->join('web_material_history', 'web_purchase_material_history.web_material_history_id = web_material_history.web_material_history_id');
		$this->db->join('inventory_reciept_po', 'web_purchase_material_history.web_purchase_material_history_id = inventory_reciept_po.web_purchase_material_history_id','left outer');
		$this->db->join('store_location', 'inventory_reciept_po.store_location_id = store_location.store_location_id');
		$this->db->join('store_sub_shelf', 'inventory_reciept_po.store_sub_shelf_id = store_sub_shelf.store_sub_shelf_id');
		
		$this->db->where('web_purchase_order.po_number',$po_number);
		$this->db->where('web_purchase_order.ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_ponumber_join_reciept_location_v2($shop_id,$po_number){

		$sql = "select * from (
					SELECT 
					web_purchase_order.web_purchase_order_id as purchase_order_id, 
					web_purchase_material_history.web_purchase_material_id as purchase_material_id,
					web_purchase_material_history.web_purchase_material_history_id as web_purchase_material_history_id,
					material_name,
					material_size,
					material_unit_price,
					qty,
					store_location_name,
					store_shelf_name
					FROM web_purchase_order
					JOIN web_purchase_material_history ON web_purchase_order.web_purchase_order_id = web_purchase_material_history.web_purchase_order_id
					JOIN web_material_history ON web_purchase_material_history.web_material_history_id = web_material_history.web_material_history_id
					LEFT OUTER JOIN inventory_reciept_po ON web_purchase_material_history.web_purchase_material_history_id = inventory_reciept_po.web_purchase_material_history_id
					JOIN store_location ON inventory_reciept_po.store_location_id = store_location.store_location_id
					JOIN store_shelf ON inventory_reciept_po.store_shelf_id = store_shelf.store_shelf_id
					WHERE web_purchase_order.po_number = '".$po_number."' AND web_purchase_order.ShopID = '".$shop_id."'
					) t1 inner join 
					(SELECT web_purchase_material_id, MAX(web_purchase_material_history_id) AS max_id
				    FROM web_purchase_material_history where po_number = '".$po_number."'
				    GROUP BY web_purchase_material_id) t2
					on t1.web_purchase_material_history_id = t2.max_id";

		$query = $this->db->query($sql);
		return $query->result_array();			
	}

	function select_by_matid_his($ShopID,$web_material_id,$supplier_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$this->db->where('web_material_history_lasted.web_material_id',$web_material_id);
		$this->db->where('material_map_supplier_history_lasted.web_supplier_id',$web_supplier_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function select_by_shop_id_limit($ShopID,$limit){

		$this->db->select("*,FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ) as po_cdate");
		$this->db->from('web_purchase_order');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_purchase_order.ShopID',$ShopID);
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function select_by_shop_id_search($shopid,$po_search,$po_status,$start_date,$stop_date,$per_page,$offset,$sortby,$sorttype){

		$this->db->select("*,FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ) as po_cdate");
		$this->db->from('web_purchase_order');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_purchase_order.ShopID',$shopid);
		if($po_search != ""){
			$this->db->like('po_number',$po_search);
		}
		if($po_status != "All"){
			$this->db->where('status',$po_status);
		}
		if(($start_date != "")and($stop_date != "")){
			$this->db->where("convert(date,convert(varchar, web_purchase_order.cdate, 23))>=convert(date,convert(varchar, '".$start_date."', 23)) and convert(date,convert(varchar, web_purchase_order.cdate, 23))<=convert(date,convert(varchar, '".$stop_date."', 23))");
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_po_search($shopid,$po_search,$po_status){

		$this->db->select("FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ) as po_cdate,web_purchase_order.po_number,supplier_name,web_purchase_order.web_purchase_order_id,sum(web_material_history_lasted.material_unit_price) as po_price");
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material', 'web_purchase_order.web_purchase_order_id = web_purchase_material.web_purchase_order_id');
		$this->db->join('web_material_history_lasted', 'web_purchase_material.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_purchase_order.ShopID',$shopid);
		$this->db->where('web_purchase_order.status',$po_status);
		if($po_search != ""){
			$this->db->like('web_purchase_order.po_number',$po_search);
		}

		$this->db->group_by("FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ),web_purchase_order.po_number,supplier_name,web_purchase_order.web_purchase_order_id");
		$this->db->order_by('web_purchase_order.po_number','ASC');

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_web_purchase_order_id($web_purchase_order_id){

		$this->db->select("FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ) as po_cdate,web_purchase_order.po_number,supplier_name,web_purchase_order.web_purchase_order_id,sum(web_material_history_lasted.material_unit_price) as po_price");
		$this->db->from('web_purchase_order');
		$this->db->join('web_purchase_material', 'web_purchase_order.web_purchase_order_id = web_purchase_material.web_purchase_order_id');
		$this->db->join('web_material_history_lasted', 'web_purchase_material.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_supplier', 'web_purchase_order.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_purchase_order.web_purchase_order_id',$web_purchase_order_id);

		$this->db->group_by("FORMAT ( web_purchase_order.cdate , 'dd-MM-yyyy hh:mm:ss' ),web_purchase_order.po_number,supplier_name,web_purchase_order.web_purchase_order_id");
		$this->db->order_by('web_purchase_order.po_number','ASC');

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();

	}

}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */