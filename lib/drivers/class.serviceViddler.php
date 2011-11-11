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

		public function getEmbedCode($data, $options) {
			$player = null;
			$xml_data = $data['oembed_xml'];

			if(empty($xml_data)) return false;

			$xml = new DOMDocument();

			if (@$xml->loadXML($xml_data)) {
				$player = $xml->getElementsByTagName('html')->item(0)->saveXML();

				if ($options['location'] == 'sidebar') {
					// replace height and width to make it fit in the backend
					$w = $this->getEmbedSize($options, 'width');
					$h = $this->getEmbedSize($options, 'height');

					$player = preg_replace(
						array('/width="([^"]*)"/', '/height="([^"]*)"/'),
						array("width=\"{$w}\"", "height=\"{$h}\""),
						$player
					);
				}

				return $player;
			}

			return false;
		}

		public function getOEmbedXmlApiUrl($params) {
			$url = trim($params['url']);

			// trying to fix url with # in it
			if (strpos($params['url'], '#') !== FALSE) {
				// split on every # or /
				$exploded = preg_split('/[\/#]/', $url);

				$url = self::BASE_URL . $exploded[count($exploded)-1];
			}

			return 'http://lab.viddler.com/services/oembed/?type=simple&format=xml&url=' . $url;
		}

		public function getIdTagName() {
			return null; // will use url as id
		}
	}