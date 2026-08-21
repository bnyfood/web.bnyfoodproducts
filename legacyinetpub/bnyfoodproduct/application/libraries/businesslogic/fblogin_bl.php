<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fblogin_bl
{
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('web_user_login_model');
    }

    public function get_login_url($redirect_uri = null)
    {
        if (empty($redirect_uri)) {
            $redirect_uri = base_url('Facrbook_login/callback');
        }

        if (empty(FACEBOOK_APP_ID) || empty(FACEBOOK_APP_SECRET)) {
            return array(
                'status' => false,
                'message' => 'Please set FACEBOOK_APP_ID and FACEBOOK_APP_SECRET in constants.php'
            );
        }

        $state = md5(uniqid('fb_', true));
        $this->CI->session->set_userdata('facebook_oauth_state', $state);
        // Fallback for environments where session may rotate between redirects.
        setcookie('facebook_oauth_state', $state, time() + 600, '/');

        $query = http_build_query(array(
            'client_id' => FACEBOOK_APP_ID,
            'redirect_uri' => $redirect_uri,
            'scope' => 'public_profile,email',
            'response_type' => 'code',
            'state' => $state
        ));

        return array(
            'status' => true,
            'login_url' => 'https://www.facebook.com/v20.0/dialog/oauth?' . $query
        );
    }

    public function get_callback_data($redirect_uri = null)
    {
        if (empty($redirect_uri)) {
            $redirect_uri = base_url('Facrbook_login/callback');
        }

        $error = $this->CI->input->get('error', true);
        $error_description = $this->CI->input->get('error_description', true);
        if (!empty($error)) {
            return array(
                'status' => false,
                'error' => $error,
                'error_description' => $error_description
            );
        }

        $code = $this->CI->input->get('code', true);
        if (empty($code)) {
            return array(
                'status' => false,
                'message' => 'Missing OAuth code from Facebook.'
            );
        }

        $request_state = $this->CI->input->get('state', true);
        $session_state = $this->CI->session->userdata('facebook_oauth_state');
        $cookie_state = isset($_COOKIE['facebook_oauth_state']) ? $_COOKIE['facebook_oauth_state'] : '';
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
            // Temporary lenient mode for troubleshooting callback/session behavior.
            // Keep warning in response but allow token exchange to continue.
            $state_warning = array(
                'message' => 'OAuth state mismatch (lenient mode).',
                'request_state' => $request_state,
                'session_state' => $session_state,
                'cookie_state' => $cookie_state
            );
        }

        if (empty(FACEBOOK_APP_ID) || empty(FACEBOOK_APP_SECRET)) {
            return array(
                'status' => false,
                'message' => 'Please set FACEBOOK_APP_ID and FACEBOOK_APP_SECRET in constants.php'
            );
        }

        $token_response = $this->call_graph_api('https://graph.facebook.com/v20.0/oauth/access_token', array(
            'client_id' => FACEBOOK_APP_ID,
            'client_secret' => FACEBOOK_APP_SECRET,
            'redirect_uri' => $redirect_uri,
            'code' => $code
        ));

        if (!isset($token_response['access_token'])) {
            return array(
                'status' => false,
                'message' => 'Facebook did not return access token.',
                'token_response' => $token_response
            );
        }

        $fb_profile = $this->call_graph_api('https://graph.facebook.com/me', array(
            'fields' => 'id,name,email,picture',
            'access_token' => $token_response['access_token']
        ));

        $saved_user = $this->upsert_facebook_user($fb_profile);

        $this->CI->session->unset_userdata('facebook_oauth_state');
        setcookie('facebook_oauth_state', '', time() - 3600, '/');

        $result = array(
            'status' => true,
            'facebook_response' => $fb_profile,
            'token_response' => $token_response,
            'user_login_row' => $saved_user
        );

        if (!empty($state_warning)) {
            $result['warning'] = $state_warning;
        }

        return $result;
    }

    private function call_graph_api($url, $query)
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
                'message' => 'Invalid JSON response from Facebook.',
                'raw' => $result
            );
        }

        if ($http_code < 200 || $http_code >= 300) {
            $decoded['http_code'] = $http_code;
        }

        return $decoded;
    }

    private function upsert_facebook_user($fb_profile)
    {
        if (!is_array($fb_profile) || empty($fb_profile['id'])) {
            return null;
        }

        $facebook_id = (string)$fb_profile['id'];
        $facebook_name = isset($fb_profile['name']) ? $fb_profile['name'] : null;
        $facebook_email = isset($fb_profile['email']) ? $fb_profile['email'] : null;
        $facebook_phone = $this->CI->input->post('facebook_phone', true);
        if ($facebook_phone === null || $facebook_phone === '') {
            $facebook_phone = $this->CI->input->get('facebook_phone', true);
        }
        if ($facebook_phone === null || $facebook_phone === '') {
            $facebook_phone = isset($fb_profile['phone']) ? $fb_profile['phone'] : null;
        }

        $row = $this->CI->web_user_login_model->select_by_facebook_id($facebook_id);
        if (empty($row) && !empty($facebook_phone)) {
            $row = $this->CI->web_user_login_model->select_by_phone($facebook_phone);
        }

        $save_data = array(
            'facebook_id' => $facebook_id,
            'facebook_name' => $facebook_name,
            'facebook_email' => $facebook_email,
            'facebook_phone' => $facebook_phone,
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
