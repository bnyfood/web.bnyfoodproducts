<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Google Cloud Translation API (Basic v2) via REST.
 * Requires GOOGLE_TRANSLATE_API_KEY in constants.php
 */
class Google_translate_bl
{
	private $CI;
	private $endpoint = 'https://translation.googleapis.com/language/translate/v2';

	function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * @param string $text
	 * @param string $target th|en
	 * @param string $source th|en|'' (empty = auto-detect)
	 * @param string $format text|html
	 * @return array{ok:bool,text?:string,detected?:string,error?:string}
	 */
	function translate($text, $target, $source = '', $format = 'text')
	{
		$text = (string)$text;
		$target = strtolower(trim((string)$target));
		$source = strtolower(trim((string)$source));
		$format = ($format === 'html') ? 'html' : 'text';

		if ($text === '') {
			return array('ok' => false, 'error' => 'Empty text');
		}
		if ($target !== 'th' && $target !== 'en') {
			return array('ok' => false, 'error' => 'Invalid target language');
		}
		if ($source !== '' && $source !== 'th' && $source !== 'en') {
			return array('ok' => false, 'error' => 'Invalid source language');
		}
		if ($source !== '' && $source === $target) {
			return array('ok' => true, 'text' => $text, 'detected' => $source);
		}

		$key = '';
		if (defined('GOOGLE_TRANSLATE_API_KEY')) {
			$key = trim((string)GOOGLE_TRANSLATE_API_KEY);
		}
		if ($key === '' || $key === 'YOUR_GOOGLE_TRANSLATE_API_KEY') {
			return array('ok' => false, 'error' => 'Google Translate API key not configured (GOOGLE_TRANSLATE_API_KEY)');
		}

		$payload = array(
			'q' => $text,
			'target' => $target,
			'format' => $format
		);
		if ($source !== '') {
			$payload['source'] = $source;
		}

		$url = $this->endpoint.'?key='.rawurlencode($key);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$raw = curl_exec($ch);
		$errno = curl_errno($ch);
		$err = curl_error($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($errno) {
			return array('ok' => false, 'error' => 'cURL error: '.$err);
		}

		$data = json_decode($raw, true);
		if ($code < 200 || $code >= 300) {
			$msg = 'HTTP '.$code;
			if (!empty($data['error']['message'])) {
				$msg = $data['error']['message'];
			}
			return array('ok' => false, 'error' => $msg);
		}

		$translated = '';
		$detected = $source;
		if (!empty($data['data']['translations'][0]['translatedText'])) {
			$translated = (string)$data['data']['translations'][0]['translatedText'];
		}
		if (!empty($data['data']['translations'][0]['detectedSourceLanguage'])) {
			$detected = (string)$data['data']['translations'][0]['detectedSourceLanguage'];
		}

		if ($translated === '') {
			return array('ok' => false, 'error' => 'Empty translation response');
		}

		// Google HTML-escapes some entities when format=text
		if ($format === 'text') {
			$translated = html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		return array(
			'ok' => true,
			'text' => $translated,
			'detected' => $detected
		);
	}
}
