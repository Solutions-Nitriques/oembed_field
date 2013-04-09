<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	/**
	 * Instagram Driver
	 * 
	 * @since 1.7
	 */
	class serviceInstagram extends ServiceDriver {

		public function __construct() {
			parent::__construct('Instagram', array('instagram.com', 'instagr.am'));
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'http://api.instagram.com/oembed?url=' . $url . $query_params;
		}

		public function getIdTagName() {
			return 'media_id';
		}
		
		public function getAPIFormat() {
			return 'json';
		}

		public function getThumbnailTagName() {
			return 'url';
		}
		
		public function getTitleTagName() {
			return 'title';
		}
	}
