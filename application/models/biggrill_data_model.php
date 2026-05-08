<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class biggrill_data_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('biggrill_data', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('biggrill_data_id',$id);
		$this->db->update('biggrill_data',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('biggrill_data_id',$id);
		$this->db->delete('biggrill_data');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('biggrill_data');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('biggrill_data');
			$this->db->where('biggrill_data_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

    function select_by_order_id($order_id){
			$this->db->select('*');
			$this->db->from('biggrill_data');
			$this->db->where('order_id',$order_id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

    function select_point_by_phone($web_user_login_id)
    {
        $web_user_login_id = (int) $web_user_login_id;
        if ($web_user_login_id <= 0) {
            return 0;
        }

        $this->db->select('web_user_phone');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('is_verify', 1);
        $phone_rows = $this->db->get()->result_array();

        if (empty($phone_rows)) {
            return 0;
        }

        $phones = array();
        foreach ($phone_rows as $r) {
            $p = preg_replace('/[^0-9]/', '', (string) (isset($r['web_user_phone']) ? $r['web_user_phone'] : ''));
            if ($p !== '') {
                $phones[$p] = $p;
            }
        }
        $phones = array_values($phones);
        if (empty($phones)) {
            return 0;
        }

        $tz = new DateTimeZone('Asia/Bangkok');
        $now = new DateTime('now', $tz);
        $n = (int) $now->format('N'); // 1 = Monday
        $start = clone $now;
        $start->modify('-' . ($n - 1) . ' days');
        $start->setTime(10, 0, 0);
        if ($now < $start) {
            $start->modify('-7 days');
        }
        $end = clone $start;
        $end->modify('+6 days');
        $end->setTime(23, 59, 59);

        $this->db->select('SUM(FLOOR(ISNULL(price, 0) / 500.0)) AS total_point', false);
        $this->db->from('biggrill_data');
        $this->db->where_in('cus_phone', $phones);
        $this->db->where('ctime >=', $start->format('Y-m-d H:i:s'));
        $this->db->where('ctime <=', $end->format('Y-m-d H:i:s'));
        $query = $this->db->get();
        $row = $query->row_array();

        if (empty($row) || !isset($row['total_point']) || $row['total_point'] === null) {
            return 0;
        }
        return (int) $row['total_point'];
    }


}


