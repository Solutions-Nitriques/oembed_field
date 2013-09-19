<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceEmbedly extends ServiceDriver {

		public function __construct() {
			parent::__construct('Embed.ly', 'embed.ly');
		}

		public function supportsSSL() {
			return true;
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'http://api.embed.ly/1/oembed?format=xml&url=' . $url . $query_params;
		}

		public function getIdTagName() {
			return null;
		}
	}
