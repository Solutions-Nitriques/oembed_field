<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceIframely extends ServiceDriver {

		public function __construct() {
			parent::__construct('Iframely', 'iframely.com');
		}

		public function supportsSSL() {
			return true;
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'http://open.iframe.ly/api/oembed?format=xml&origin=@iframely&url=' . $url . $query_params;
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
	}
