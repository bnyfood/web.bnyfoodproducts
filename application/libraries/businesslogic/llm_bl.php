<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Llm_bl
{
	function chat($settings, $system, $user)
	{
		$provider = strtolower(trim((string)(isset($settings['provider']) ? $settings['provider'] : 'openai')));
		$model = trim((string)(isset($settings['model_name']) ? $settings['model_name'] : ''));
		$key = trim((string)(isset($settings['api_key']) ? $settings['api_key'] : ''));
		if ($key === '') {
			return array('ok' => false, 'error' => 'missing_api_key', 'text' => '');
		}
		if ($provider === 'gemini') {
			return $this->gemini($key, $model !== '' ? $model : 'gemini-2.0-flash', $system, $user);
		}
		if ($provider === 'anthropic') {
			return $this->anthropic($key, $model !== '' ? $model : 'claude-sonnet-4-5', $system, $user);
		}
		return $this->openai($key, $model !== '' ? $model : 'gpt-4o-mini', $system, $user);
	}

	function openai($key, $model, $system, $user)
	{
		$payload = json_encode(array(
			'model' => $model,
			'messages' => array(
				array('role' => 'system', 'content' => $system),
				array('role' => 'user', 'content' => $user)
			),
			'temperature' => 0.4
		), JSON_UNESCAPED_UNICODE);
		$raw = $this->http_json('https://api.openai.com/v1/chat/completions', $payload, array(
			'Authorization: Bearer '.$key
		));
		if ($raw === false) {
			return array('ok' => false, 'error' => 'http_fail', 'text' => '');
		}
		$data = json_decode($raw, true);
		$text = '';
		if (isset($data['choices'][0]['message']['content'])) {
			$text = $data['choices'][0]['message']['content'];
		}
		if ($text === '') {
			$err = isset($data['error']['message']) ? $data['error']['message'] : 'empty_response';
			return array('ok' => false, 'error' => $err, 'text' => '');
		}
		return array('ok' => true, 'error' => '', 'text' => trim($text));
	}

	function gemini($key, $model, $system, $user)
	{
		$url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.rawurlencode($key);
		$payload = json_encode(array(
			'systemInstruction' => array(
				'parts' => array(array('text' => $system))
			),
			'contents' => array(
				array('role' => 'user', 'parts' => array(array('text' => $user)))
			)
		), JSON_UNESCAPED_UNICODE);
		$raw = $this->http_json($url, $payload, array());
		if ($raw === false) {
			return array('ok' => false, 'error' => 'http_fail', 'text' => '');
		}
		$data = json_decode($raw, true);
		$text = '';
		if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
			$text = $data['candidates'][0]['content']['parts'][0]['text'];
		}
		if ($text === '') {
			$err = isset($data['error']['message']) ? $data['error']['message'] : 'empty_response';
			return array('ok' => false, 'error' => $err, 'text' => '');
		}
		return array('ok' => true, 'error' => '', 'text' => trim($text));
	}

	function anthropic($key, $model, $system, $user)
	{
		$payload = json_encode(array(
			'model' => $model,
			'max_tokens' => 1024,
			'system' => $system,
			'messages' => array(
				array('role' => 'user', 'content' => $user)
			)
		), JSON_UNESCAPED_UNICODE);
		$raw = $this->http_json('https://api.anthropic.com/v1/messages', $payload, array(
			'x-api-key: '.$key,
			'anthropic-version: 2023-06-01'
		));
		if ($raw === false) {
			return array('ok' => false, 'error' => 'http_fail', 'text' => '');
		}
		$data = json_decode($raw, true);
		$text = '';
		if (isset($data['content'][0]['text'])) {
			$text = $data['content'][0]['text'];
		}
		if ($text === '') {
			$err = isset($data['error']['message']) ? $data['error']['message'] : 'empty_response';
			return array('ok' => false, 'error' => $err, 'text' => '');
		}
		return array('ok' => true, 'error' => '', 'text' => trim($text));
	}

	function http_json($url, $payload, $extra_headers)
	{
		$headers = array_merge(array('Content-Type: application/json'), $extra_headers);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$out = curl_exec($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if ($errno !== 0 || $out === false) {
			return false;
		}
		return $out;
	}
}
