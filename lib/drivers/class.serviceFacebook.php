<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceFacebook extends ServiceDriver {

		public function __construct() {
			parent::__construct('Facebook', array('facebook.com', 'fb.com'));
		}

		public function getAPIFormat() {
			return 'json';
		}

		public function supportsSSL() {
			return true;
		}

		public function getRootTagName() {
			return 'data';
		}

		public function getNeededUrlsToJITimages() {
			return array(
				'*.fbcdn.net'
			);
		}

		public function getOEmbedApiUrl($params) {
			$url = trim($params['url']);
			$query_params = $params['query_params'];
			$isVideo = preg_match('/\/video\.php/', $url) || preg_match('/\/videos\//', $url);
			$endpoint = $isVideo ? 'video' : 'post';
			$url = urlencode($url);
			return "https://www.facebook.com/plugins/$endpoint/oembed.json/?url=$url$query_params";
		}

		public function getEmbedCode($data, $options) {
			$data = @json_decode($data['oembed_xml'], true);
			if (is_array($data)) {
				return $data['html'];
			}
		}
	}