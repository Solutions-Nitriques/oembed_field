<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceNoembed extends ServiceDriver {

		public function __construct() {
			parent::__construct('Noembed', 'noembed.com');
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'https://noembed.com/embed?url=' . $url . $query_params;
		}

		public function getAPIFormat() {
			return 'json';
		}

		/**
		 * This driver is non-native (third party)
		 */
		public function isNative() {
			return false;
		}

		public function isMatch($url) {
			// since this service accepts lots of providers,
			// even not oEmbed compatible, just check that it's
			// an url
			return filter_var($url, FILTER_VALIDATE_URL);
		}

		public function getEmbedCode($data, $options) {
			$data = @json_decode($data['oembed_xml'], true);
			if (is_array($data)) {
				return $data['html'];
			}
			return false;
		}
	}
