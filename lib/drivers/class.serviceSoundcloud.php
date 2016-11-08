<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	/**
	 * Soundcloud Driver
	 */
	class serviceSoundcloud extends ServiceDriver {

		public function __construct() {
			parent::__construct('Soundcloud', 'soundcloud.com');
		}

		public function getOEmbedApiUrl($params) {
			$query_params = $params['query_params'];
			return 'https://soundcloud.com/oembed?url=' . trim($params['url']) . $query_params;
		}

		public function supportsSSL() {
			return true;
		}

	}
