<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceYouTube extends ServiceDriver {

		public function __construct() {
			parent::__construct('YouTube', 'youtube.com');
		}

		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.0',
				'release-date'	=> '2011-07-17',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				)
	 		);
		}

		public function getEmbedCode($data, $options) {
			$xml = new DOMDocument();
			$xml->loadXML($data['oembed_xml']);

			return $xml->getElementsByTagName('html')->item(0)->nodeValue;
		}

		public function getOEmbedXmlApiUrl($params) {
			return 'http://www.youtube.com/oembed?format=xml&url=' . $params['url'];
		}


		public function getIdTagName() {
			return null; // will use url as id
		}
	}