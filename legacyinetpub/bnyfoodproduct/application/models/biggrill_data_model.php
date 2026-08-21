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

	function update_by_order_id($data, $order_id){
		$this->db->where('order_id', $order_id);
		$this->db->update('biggrill_data', $data);
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

        $phones = array();

        $this->db->select('web_user_phone');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('is_verify', 1);
        $phone_rows = $this->db->get()->result_array();
        foreach ($phone_rows as $r) {
            $p = preg_replace('/[^0-9]/', '', (string) (isset($r['web_user_phone']) ? $r['web_user_phone'] : ''));
            if ($p !== '') {
                $phones[$p] = $p;
            }
        }

        $this->db->select('web_user_phone');
        $this->db->from('web_user_login');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $login_row = $this->db->get()->row_array();
        if (!empty($login_row['web_user_phone'])) {
            $p = preg_replace('/[^0-9]/', '', (string) $login_row['web_user_phone']);
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
        $end->modify('+7 days');
        $end->modify('-1 second');

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

    function select_reward_candidates_by_period($period_start, $period_end)
    {
        if (empty($period_start) || empty($period_end)) {
            return array();
        }

        $this->db->select('cus_phone, SUM(ISNULL(price, 0)) AS total_price', false);
        $this->db->from('biggrill_data');
        $this->db->where('ctime >=', $period_start);
        $this->db->where('ctime <=', $period_end);
        $this->db->where("LTRIM(RTRIM(ISNULL(cus_phone, ''))) <> ''", null, false);
        $this->db->group_by('cus_phone');
        $rows = $this->db->get()->result_array();

        $grouped = array();
        foreach ($rows as $row) {
            $phone = preg_replace('/[^0-9]/', '', (string) (isset($row['cus_phone']) ? $row['cus_phone'] : ''));
            if ($phone === '') {
                continue;
            }

            $total_price = isset($row['total_price']) ? (float) $row['total_price'] : 0.0;
            if (!isset($grouped[$phone])) {
                $grouped[$phone] = 0.0;
            }
            $grouped[$phone] += $total_price;
        }

        $candidates = array();
        foreach ($grouped as $phone => $total_price) {
            $ticket_count = (int) floor($total_price / 500);
            if ($ticket_count <= 0) {
                continue;
            }

            $candidates[] = array(
                'cus_phone' => $phone,
                'total_price' => $total_price,
                'ticket_count' => $ticket_count,
            );
        }

        return $candidates;
    }

    function select_by_status_last_arr($arr_status,$limit){

		$this->db->select("*,FORMAT ( [ctime] , 'yyyy-MM' ) as yyyymm , ctime as create_time");
		$this->db->from('biggrill_data');
		$this->db->where_not_in('status',$arr_status);
		$this->db->where('order_id not in (select order_id from inwshop_taxinvoiceid)');

		$this->db->order_by('ctime','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_search($params)
	{
		$allowed_fields = array('order_id', 'cus_name', 'cus_phone', 'status', 'trackingid', 'taxinvoiceid');
		$allowed_sort = array(
			'order_id', 'cus_name', 'cus_phone', 'status', 'price', 'delivery', 'discount',
			'amount_include_vat', 'amount_exclude_vat', 'vat', 'trackingid', 'taxinvoiceID', 'is_void', 'ctime', 'biggrill_data_id'
		);

		$search_field = isset($params['search_field']) ? $params['search_field'] : '';
		$search_text = isset($params['search_text']) ? trim($params['search_text']) : '';
		$is_void = isset($params['is_void']) ? $params['is_void'] : '';
		$sortby = isset($params['sortby']) ? $params['sortby'] : '';
		$sorttype = isset($params['sorttype']) ? $params['sorttype'] : '';
		$offset = isset($params['offset']) ? (int) $params['offset'] : 0;
		$per_page = isset($params['per_page']) ? (int) $params['per_page'] : 20;
		$date_start = isset($params['date_start']) ? $params['date_start'] : '';
		$date_end = isset($params['date_end']) ? $params['date_end'] : '';

		$this->db->select('biggrill_data.*, inwshop_taxinvoiceid.taxinvoiceID', FALSE);
		$this->db->from('biggrill_data');
		$this->db->join('inwshop_taxinvoiceid', 'biggrill_data.order_id = inwshop_taxinvoiceid.order_id', 'left');

		if ($search_text !== '' && in_array($search_field, $allowed_fields, TRUE)) {
			if ($search_field === 'taxinvoiceid') {
				$this->db->like('inwshop_taxinvoiceid.taxinvoiceID', $search_text);
			} else {
				$this->db->like('biggrill_data.' . $search_field, $search_text);
			}
		}

		if ($is_void !== '' && $is_void !== NULL) {
			$this->db->where('ISNULL(biggrill_data.is_void, 0) = ' . (int) $is_void, NULL, FALSE);
		}

		if ($date_start !== '' && $date_end !== '') {
			$this->db->where('biggrill_data.ctime >=', $date_start);
			$this->db->where('biggrill_data.ctime <=', $date_end);
		}

		if ($sortby !== '' && in_array($sortby, $allowed_sort, TRUE)) {
			$sorttype = strtolower($sorttype) === 'desc' ? 'DESC' : 'ASC';
			if ($sortby === 'taxinvoiceID') {
				$this->db->order_by('inwshop_taxinvoiceid.taxinvoiceID', $sorttype);
			} else {
				$this->db->order_by('biggrill_data.' . $sortby, $sorttype);
			}
		} else {
			$this->db->order_by('biggrill_data.biggrill_data_id', 'DESC');
		}

		$this->db->limit($per_page, $offset);
		$query = $this->db->get();
		return $query->result_array();
	}


}


