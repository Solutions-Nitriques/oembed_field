<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceTwitter extends ServiceDriver {

		public function __construct() {
			parent::__construct('Twitter', 'twitter.com');
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'https://publish.twitter.com/oembed?url=' . $url . $query_params;
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

		public function getEmbedCode($data, $options) {
			$embed = @parent::getEmbedCode($data, $options);
			if (!$embed) {
				$data = @json_decode($data['oembed_xml'], true);
				if (is_array($data)) {
					return $data['html'];
				}
			}
			return $embed;
		}
	}
