<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class testcontroller extends CI_Controller {

	function __construct() 
	{
		//:[Auto call parent construct]
        parent::__construct();
		//$this->load->model('test_model');
		
		
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    
    public function testlaz()
    {

     include APPPATH . 'third_party\api\lazada\LazopSdk.php';

     /*$c = new LazopClient('https://api.lazada.test/rest', '123793', 'NPb6xpPp3EouAS0uhqYtVG0dNEXw6hAN');
    $request = new LazopRequest('/mock/api/get');
    $request->addApiParam('api_id',1);
    $request->addHttpHeaderParam('cx','test');
    
    var_dump($c->execute($request));
    */


    $c = new LazopClient('https://api.lazada.test/rest','123793','NPb6xpPp3EouAS0uhqYtVG0dNEXw6hAN');
$request = new LazopRequest('/order/document/get','GET');
$request->addApiParam('doc_type','shippingLabel');
$request->addApiParam('order_item_ids','[279709, 279709]');
var_dump($c->execute($request, '50000900135c1pxTpiBUQHzlK9sVnthc2IxgoQcrrh6jCT61f3f4271pwceFRhUx'));

    }



	public function testit()
	{
		


		//echo APPPATH."hello wortld!";
		
		$arr = $this->test_model->select_Orders();
		print_r($arr);
		//$this->load->view('welcome_message');




$return_arr = array("id" => '1234',
                   "username" => 'phuketvending',
                   "name" => 'seubsak sahaworaphan',
                  "email" => 'phuketvending@gmail.com');


// Encoding array in JSON format
echo json_encode($return_arr);


	}



}
