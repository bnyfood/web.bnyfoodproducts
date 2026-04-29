<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Googlelogin_bl
{
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('web_user_login_model');
    }

    public function get_login_url($redirect_uri = null)
    {
        if (empty($redirect_uri)) {
            $redirect_uri = defined('GOOGLE_GOOGLE_LOGIN_REDIRECT') && GOOGLE_GOOGLE_LOGIN_REDIRECT
                ? GOOGLE_GOOGLE_LOGIN_REDIRECT
                : base_url('users/google_login');
        }

        if (empty(GOOGLE_Client_ID) || empty(GOOGLE_SECRET_KEY)) {
            return array(
                'status' => false,
                'message' => 'Please set GOOGLE_Client_ID and GOOGLE_SECRET_KEY in constants.php'
            );
        }

        $state = md5(uniqid('gg_', true));
        $this->CI->session->set_userdata('google_oauth_state', $state);
        setcookie('google_oauth_state', $state, time() + 600, '/');

        $query = http_build_query(array(
            'client_id' => GOOGLE_Client_ID,
            'redirect_uri' => $redirect_uri,
            'scope' => 'openid email profile',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state
        ));

        return array(
            'status' => true,
            'login_url' => 'https://accounts.google.com/o/oauth2/v2/auth?' . $query
        );
    }

    public function get_callback_data($redirect_uri = null)
    {
        if (empty($redirect_uri)) {
            $redirect_uri = defined('GOOGLE_GOOGLE_LOGIN_REDIRECT') && GOOGLE_GOOGLE_LOGIN_REDIRECT
                ? GOOGLE_GOOGLE_LOGIN_REDIRECT
                : base_url('users/google_login');
        }

        $error = $this->CI->input->get('error', true);
        if (!empty($error)) {
            return array(
                'status' => false,
                'error' => $error,
                'error_description' => $this->CI->input->get('error_description', true)
            );
        }

        $code = $this->CI->input->get('code', true);
        if (empty($code)) {
            return array(
                'status' => false,
                'message' => 'Missing OAuth code from Google.'
            );
        }

        $request_state = $this->CI->input->get('state', true);
        $session_state = $this->CI->session->userdata('google_oauth_state');
        $cookie_state = isset($_COOKIE['google_oauth_state']) ? $_COOKIE['google_oauth_state'] : '';
        $state_ok = false;
        if (!empty($request_state)) {
            if (!empty($session_state) && $request_state === $session_state) {
                $state_ok = true;
            } elseif (!empty($cookie_state) && $request_state === $cookie_state) {
                $state_ok = true;
            }
        }

        $state_warning = null;
        if (!$state_ok) {
            $state_warning = array(
                'message' => 'OAuth state mismatch (lenient mode).',
                'request_state' => $request_state,
                'session_state' => $session_state,
                'cookie_state' => $cookie_state
            );
        }

        if (empty(GOOGLE_Client_ID) || empty(GOOGLE_SECRET_KEY)) {
            return array(
                'status' => false,
                'message' => 'Please set GOOGLE_Client_ID and GOOGLE_SECRET_KEY in constants.php'
            );
        }

        $token_response = $this->request_token($code, $redirect_uri);
        if (!isset($token_response['access_token'])) {
            return array(
                'status' => false,
                'message' => 'Google did not return access token.',
                'token_response' => $token_response
            );
        }

        $google_profile = $this->call_google_api('https://www.googleapis.com/oauth2/v2/userinfo', array(
            'access_token' => $token_response['access_token']
        ));

        $saved_user = $this->upsert_google_user($google_profile);

        $this->CI->session->unset_userdata('google_oauth_state');
        setcookie('google_oauth_state', '', time() - 3600, '/');

        $result = array(
            'status' => true,
            'google_response' => $google_profile,
            'token_response' => $token_response,
            'user_login_row' => $saved_user
        );

        if (!empty($state_warning)) {
            $result['warning'] = $state_warning;
        }

        return $result;
    }

    private function request_token($code, $redirect_uri)
    {
        $payload = http_build_query(array(
            'code' => $code,
            'client_id' => GOOGLE_Client_ID,
            'client_secret' => GOOGLE_SECRET_KEY,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code'
        ));

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
        ));

        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return array(
                'status' => false,
                'message' => 'Curl error',
                'detail' => $error
            );
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return array(
                'status' => false,
                'message' => 'Invalid JSON response from Google token endpoint.',
                'raw' => $result
            );
        }

        if ($http_code < 200 || $http_code >= 300) {
            $decoded['http_code'] = $http_code;
        }

        return $decoded;
    }

    private function call_google_api($url, $query)
    {
        $full_url = $url . '?' . http_build_query($query);

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20
        ));

        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return array(
                'status' => false,
                'message' => 'Curl error',
                'detail' => $error
            );
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            return array(
                'status' => false,
                'message' => 'Invalid JSON response from Google.',
                'raw' => $result
            );
        }

        if ($http_code < 200 || $http_code >= 300) {
            $decoded['http_code'] = $http_code;
        }

        return $decoded;
    }

    private function upsert_google_user($google_profile)
    {
        if (!is_array($google_profile) || empty($google_profile['id'])) {
            return null;
        }

        $google_id = (string)$google_profile['id'];
        $google_name = isset($google_profile['name']) ? $google_profile['name'] : null;
        $google_email = isset($google_profile['email']) ? $google_profile['email'] : null;
        $google_phone = $this->CI->input->post('google_phone', true);
        if ($google_phone === null || $google_phone === '') {
            $google_phone = $this->CI->input->get('google_phone', true);
        }
        if ($google_phone === null || $google_phone === '') {
            $google_phone = isset($google_profile['phone']) ? $google_profile['phone'] : null;
        }

        $row = $this->CI->web_user_login_model->select_by_google_id($google_id);
        if (empty($row) && !empty($google_phone)) {
            $row = $this->CI->web_user_login_model->select_by_phone($google_phone);
        }

        $save_data = array(
            'google_id' => $google_id,
            'google_name' => $google_name,
            'google_email' => $google_email,
            'google_phone' => $google_phone,
            'last_login_time' => DATE_TIME_NOW
        );

        if (empty($row)) {
            $save_data['cdate'] = DATE_TIME_NOW;
            $insert_id = $this->CI->web_user_login_model->insert($save_data);
            return $this->CI->web_user_login_model->select_by_id($insert_id);
        }

        $this->CI->web_user_login_model->update_by_id($row['web_user_login_id'], $save_data);
        return $this->CI->web_user_login_model->select_by_id($row['web_user_login_id']);
    }
}
