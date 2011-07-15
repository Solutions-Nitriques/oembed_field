<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceFlickr extends ServiceDriver {

		public function __construct() {
			parent::__construct('Flickr', 'flickr.com');
		}
		
		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.0',
				'release-date'	=> '2011-07-15',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				)
	 		);
		}

		public function getEmbedCode($data, $options) {
			return vsprintf('<img src="%s" width="%d" height="%d" />',
							array($data['res_id'], $options['width'], $options['height']));
		}

		public function getOEmbedXmlApiUrl($params) {
			return 'http://www.flickr.com/services/oembed?url=' . $params['url'];
		}

			
		public function getIdTagName() {
			return 'url';
		}
	}