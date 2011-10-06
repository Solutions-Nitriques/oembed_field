<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceYouTube extends ServiceDriver {

		const BASE_URL = "http://youtu.be/";

		public function __construct() {
			parent::__construct('YouTube', 'youtube.com');
		}

		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.2',
				'release-date'	=> '2011-09-27',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				)
	 		);
		}

		public function getEmbedCode($data, $options) {

			$player = null;

			$xml_data = $data['oembed_xml'];

			//var_dump($data);die;

			if (!empty($xml_data)) {
				$xml = new DOMDocument();

				if (@$xml->loadXML($xml_data)) {

					$player = $xml->getElementsByTagName('html')->item(0)->nodeValue;

					if ($options['location'] == 'sidebar') {
						// replace height and width to make it fit in the backend
						$w = $this->getEmbedSize($options, 'width');
						$h = $this->getEmbedSize($options, 'height');

						$player = preg_replace(
							array('/width="([^"]*)"/', '/height="([^"]*)"/'),
							array("width=\"{$w}\"", "height=\"{$h}\""), $player);
					}

				}
			}

			return $player;
		}

		public function getOEmbedXmlApiUrl($params) {
			$url = trim($params['url']);

			// trying to fix url with # in it
			if (strpos($params['url'], '#') !== FALSE) {
				// split on every # or /
				$exploded = preg_split('/[\/#]/', $url);

				$url = self::BASE_URL . $exploded[count($exploded)-1];
			}

			//var_dump($url); die;

			return 'http://www.youtube.com/oembed?format=xml&url=' . $url;
		}


		public function getIdTagName() {
			return null; // will use url as id
		}
	}