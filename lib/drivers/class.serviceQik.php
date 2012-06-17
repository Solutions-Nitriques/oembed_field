<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceQik extends ServiceDriver {

		public function __construct() {
			parent::__construct('Qik', 'qik.com');
		}

		public function getOEmbedApiUrl($params) {
			$url = trim($params['url']);
			$query_params = $params['query_params'];
			
			return 'http://qik.com/api/oembed.xml?url=' . $url . $query_params;
		}

		//Qik Service uses "hash" as root node.
		public function getRootTagName() {
			return 'hash';
		}

	}