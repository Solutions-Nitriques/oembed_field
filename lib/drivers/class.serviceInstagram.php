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

			return 'https://api.instagram.com/oembed?url=' . $url . $query_params;
		}
		
		public function getEmbedCode($data, $options) {
			$title = General::sanitize($data['title']);
			return vsprintf('<img src="%s" width="%d" alt="%s" title="%s" />',
							array($data['thumbnail_url'],
									$this->getEmbedSize($options, 'width'),
									$title, $title
								)
					);
		}
		
		public function supportsSSL() {
			return true;
		}
		
		public function getIdTagName() {
			return 'media_id';
		}
		
		public function getAPIFormat() {
			return 'json';
		}
		
		public function getTitleTagName() {
			return 'title';
		}
		
		public function getRootTagName() {
			return 'data';
		}
	}
