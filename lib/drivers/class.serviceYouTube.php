<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceYouTube extends ServiceDriver {

		public function __construct() {
			parent::__construct('YouTube', 'youtube.com');
		}

		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.1',
				'release-date'	=> '2011-09-08',
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

			$player = $xml->getElementsByTagName('html')->item(0)->nodeValue;	
			
			if ($options['location'] == 'sidebar') {
				// replace height and width to make it fit in the backend
				$w = $this->getEmbedSize($options, 'width');
				$h = $this->getEmbedSize($options, 'height');
				
				$player = preg_replace(
					array('/width="([^"]*)"/', '/height="([^"]*)"/'), 
					array("width=\"{$w}\"", "height=\"{$h}\""), $player);
			}
			
			return $player;
		}

		public function getOEmbedXmlApiUrl($params) {
			return 'http://www.youtube.com/oembed?format=xml&url=' . $params['url'];
		}


		public function getIdTagName() {
			return null; // will use url as id
		}
	}