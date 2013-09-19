<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceTwitter extends ServiceDriver {

		public function __construct() {
			parent::__construct('Twitter', 'twitter.com');
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'https://api.twitter.com/1/statuses/oembed.xml?url=' . $url . $query_params;
		}

		public function getIdTagName() {
			return 'url';
		}
	}
