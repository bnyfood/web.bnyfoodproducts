<?php

//namespace THAIBULKSMS_API\SMS;

class SMS
{
    private $api;

    private $token;

    public function __construct($apiKey = '', $apiSecretKey = '', $options = [])
    {
        $this->token = base64_encode("$apiKey:$apiSecretKey");
        $this->api = array_key_exists('api', $options) ? $options['api'] : 'https://api-v2.thaibulksms.com';
    }

    public function sendSMS($body = [])
    {
        if (!is_array($body)) {
            die("Body rquire array");
        }

        //echo $this->token;
        //print_r($body);
        //return $this->cURL($body);
        return $this->cURL($body);
    }

    private function cURL($body = [])
    {
        print_r($body);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  "$this->api/sms",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($body),
           // CURLOPT_SSL_VERIFYHOST=>false,
            //CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' .  $this->token
            ),
        ));

        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //print_r($response);

        curl_close($curl);

        $resData = json_decode($response);
        //var_dump($resData);
        
        $res = (object)[
            'httpStatusCode' => $httpStatusCode
        ];

        if ($httpStatusCode == 201) {
            $res->data = $resData;
        } else {
            $res->error = $resData->error;
        }


        return $res;
    }

    private function cURL2($body){

        echo $this->token;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api-v2.thaibulksms.com/sms',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
         // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_POST => 1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => http_build_query($body),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.  $this->token
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        print_r($response);
    }

    private function curl3($body){
        $ch = curl_init();

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.  $this->token
          );

        $url_api = 'https://api-v2.thaibulksms.com/sms';

        curl_setopt($ch, CURLOPT_URL,$url_api);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $server_output = curl_exec ($ch);
        if ($server_output === false)
        {
            print_r('Curl error: ' . curl_error($ch));
        }

        //$info = curl_getinfo($ch);
        //echo '<br>Took ' . $info['total_time'] . ' seconds to transfer a request to ' . $info['url'];




        curl_close ($ch);

        //show error
        echo "--------------<br>";
        print_r($server_output);
        echo "--------------<br>";
    }
}
