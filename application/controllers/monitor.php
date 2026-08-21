<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Monitor extends Auth_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library("businesslogic/shopee_bl");

		$this->load->model('menu_model');
		$this->load->model('bnylog_model');


    }
     
	public function main()
	{

		$arr_input = array(
			'title' => "Monitor" 
		);

		$arr_css = array(
			'monitor_overview' => base_url().'resources/css/monitor_overview.css',
		);
		
		$arr_js = array(
	      'chartjs' => base_url().'global/vendor/chart-js/Chart.min.js',
	      'overview' => base_url().'resources/js/monitor/overview.js',
	      'lazada_api' => base_url().'resources/js/monitor/lazada_api.js',
	      'litetime_token' => base_url().'resources/js/monitor/litetime_token.js',
	    ); 	        

		$link=$this->shopee_bl->get_authenticatrion_link(SHOPEE_PATNERKEY,'1001849');

	    $data=array(
	    	'link'=>$link,
	    	'ads_webhook_url' => base_url().'ads_webhook/ingest',
	    	'ads_webhook_ping' => base_url().'ads_webhook/ping',
	    	'ads_webhook_secret' => defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '',
	    	'platform_webhook_ping' => base_url().'platform_webhook/ping',
	    	'platform_webhook_lazada' => base_url().'platform_webhook/lazada',
	    	'platform_webhook_shopee' => base_url().'platform_webhook/shopee',
	    	'platform_webhook_tiktok' => base_url().'platform_webhook/tiktok'
   		);
				        
		$this->view_util->load_view_main('monitor/main',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	public function api_order_log(){

		$log_type = $this->uri->segment(3);
		
		$data_apis = $this->bnylog_model->select_order_lazada_last10($log_type);

		$data = array(
			'data_apis' => $data_apis
		);
		
		echo json_encode($data);
	}

	public function change_status_log(){

		$id=$this->uri->segment(3);
		$data = array(
			'log_status' => 2
		);
		$this->bnylog_model->update($data,$id);
	}

	public function overview_data()
	{
		$this->load->model('monitor_overview_model');
		$this->load->model('platform_ads_spend_model');

		$from = $this->input->get_post('from');
		$to = $this->input->get_post('to');
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$from)) {
			$from = date('Y-m-d', strtotime('-6 days'));
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$to)) {
			$to = date('Y-m-d');
		}
		if (strcmp($from, $to) > 0) {
			$tmp = $from;
			$from = $to;
			$to = $tmp;
		}

		$sales_maps = $this->monitor_overview_model->sales_by_date($from, $to);
		$fees_maps = $this->monitor_overview_model->fees_by_date($from, $to);
		$ads_rows = array();
		try {
			$ads_rows = $this->platform_ads_spend_model->sum_by_date($from, $to);
		} catch (Exception $e) {
			$ads_rows = array();
		}
		$ads_maps = array('lazada' => array(), 'shopee' => array(), 'tiktok' => array());
		foreach ($ads_rows as $row) {
			$p = strtolower((string)$row['platform']);
			if (!isset($ads_maps[$p])) {
				continue;
			}
			$ads_maps[$p][$row['d']] = (float)$row['amt'];
		}

		$labels = array();
		$sales = array('lazada' => array(), 'shopee' => array(), 'tiktok' => array());
		$ads = array('lazada' => array(), 'shopee' => array(), 'tiktok' => array());
		$fees = array('lazada' => array(), 'shopee' => array(), 'tiktok' => array());
		$expense = array('lazada' => array(), 'shopee' => array(), 'tiktok' => array());
		$combo = array(
			'sales' => array(),
			'ads' => array(),
			'fees' => array(),
			'expense' => array(),
			'return' => array()
		);
		$cur = strtotime($from.' 00:00:00');
		$end = strtotime($to.' 00:00:00');
		while ($cur <= $end) {
			$iso = date('Y-m-d', $cur);
			$labels[] = date('d/m', $cur);
			$day_sales = 0;
			$day_ads = 0;
			$day_fees = 0;
			foreach (array('lazada', 'shopee', 'tiktok') as $p) {
				$sv = isset($sales_maps[$p][$iso]) ? round($sales_maps[$p][$iso], 2) : 0;
				$av = isset($ads_maps[$p][$iso]) ? round($ads_maps[$p][$iso], 2) : 0;
				$fv = isset($fees_maps[$p][$iso]) ? round($fees_maps[$p][$iso], 2) : 0;
				$sales[$p][] = $sv;
				$ads[$p][] = $av;
				$fees[$p][] = $fv;
				$expense[$p][] = round($av + $fv, 2);
				$day_sales += $sv;
				$day_ads += $av;
				$day_fees += $fv;
			}
			$day_exp = $day_ads + $day_fees;
			$combo['sales'][] = round($day_sales, 2);
			$combo['ads'][] = round($day_ads, 2);
			$combo['fees'][] = round($day_fees, 2);
			$combo['expense'][] = round($day_exp, 2);
			$combo['return'][] = ($day_exp > 0) ? round($day_sales / $day_exp, 2) : 0;
			$cur = strtotime('+1 day', $cur);
		}

		$sum = function ($arr) {
			$t = 0;
			foreach ($arr as $v) {
				$t += (float)$v;
			}
			return round($t, 2);
		};
		$ratio = function ($rev, $exp) {
			$rev = (float)$rev;
			$exp = (float)$exp;
			if ($exp <= 0) {
				return 0;
			}
			return round($rev / $exp, 2);
		};

		$sales_tot = array(
			'lazada' => $sum($sales['lazada']),
			'shopee' => $sum($sales['shopee']),
			'tiktok' => $sum($sales['tiktok'])
		);
		$sales_tot['all'] = $sales_tot['lazada'] + $sales_tot['shopee'] + $sales_tot['tiktok'];
		$ads_tot = array(
			'lazada' => $sum($ads['lazada']),
			'shopee' => $sum($ads['shopee']),
			'tiktok' => $sum($ads['tiktok'])
		);
		$ads_tot['all'] = $ads_tot['lazada'] + $ads_tot['shopee'] + $ads_tot['tiktok'];
		$fees_tot = array(
			'lazada' => $sum($fees['lazada']),
			'shopee' => $sum($fees['shopee']),
			'tiktok' => $sum($fees['tiktok'])
		);
		$fees_tot['all'] = $fees_tot['lazada'] + $fees_tot['shopee'] + $fees_tot['tiktok'];
		$exp_tot = array(
			'lazada' => round($ads_tot['lazada'] + $fees_tot['lazada'], 2),
			'shopee' => round($ads_tot['shopee'] + $fees_tot['shopee'], 2),
			'tiktok' => round($ads_tot['tiktok'] + $fees_tot['tiktok'], 2)
		);
		$exp_tot['all'] = round($ads_tot['all'] + $fees_tot['all'], 2);

		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode(array(
			'from' => $from,
			'to' => $to,
			'labels' => $labels,
			'sales' => array(
				'series' => $sales,
				'totals' => $sales_tot
			),
			'ads' => array(
				'series' => $ads,
				'totals' => $ads_tot
			),
			'fees' => array(
				'series' => $fees,
				'totals' => $fees_tot
			),
			'expense' => array(
				'series' => $expense,
				'totals' => $exp_tot
			),
			'combo' => array(
				'series' => $combo,
				'totals' => array(
					'sales' => $sales_tot['all'],
					'ads' => $ads_tot['all'],
					'fees' => $fees_tot['all'],
					'expense' => $exp_tot['all'],
					'return' => $ratio($sales_tot['all'], $exp_tot['all']),
					'lazada' => $ratio($sales_tot['lazada'], $exp_tot['lazada']),
					'shopee' => $ratio($sales_tot['shopee'], $exp_tot['shopee']),
					'tiktok' => $ratio($sales_tot['tiktok'], $exp_tot['tiktok'])
				)
			)
		)));
	}

}