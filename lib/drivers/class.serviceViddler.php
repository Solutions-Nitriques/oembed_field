<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceViddler extends ServiceDriver {

		const BASE_URL = "http://www.viddler.com/explore";

		public function __construct() {
			parent::__construct('Viddler', 'viddler.com');
		}

		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.0',
				'release-date'	=> '2011-10-07',
				'author'		=> array(
					'name'			=> 'Andrew Minton',
					'website'		=> 'http://andrewminton.co.uk/',
					'email'			=> 'moonoo dot am (at) gmail.com'
				)
	 		);
		}

		public function getOEmbedXmlApiUrl($params) {
			$url = trim($params['url']);

			return 'http://lab.viddler.com/services/oembed/?type=simple&format=xml&url=' . $url;
		}

		public function getIdTagName() {
			return null; // will use url as id
		}
	}