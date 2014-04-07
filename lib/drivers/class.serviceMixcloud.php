<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	/**
	 * Mixcloud Driver
	 */
	class serviceMixcloud extends ServiceDriver {

		public function __construct() {
			parent::__construct('Mixcloud', 'mixcloud.com');
		}

		public function getOEmbedApiUrl($params) {
			$query_params = $params['query_params'];
			return 'http://www.mixcloud.com/oembed/?format=xml&url=' . trim($params['url']) . $query_params;
		}

		public function supportsSSL() {
			return true;
		}

	}
