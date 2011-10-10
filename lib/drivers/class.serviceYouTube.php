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

			// ref to the html string to output in the backend
			$player = null;

			// xml string from the DB
			$xml_data = $data['oembed_xml'];

			//var_dump($data);die;

			// if we have some data
			if (!empty($xml_data)) {

				// create a new DOMDocument to manipulate the XML string
				$xml = new DOMDocument();

				// if we can load the string into the document
				if (@$xml->loadXML($xml_data)) {

					// get the value of the html node
					// NOTE: this could be the XML children if the html is not encoded
					$player = $xml->getElementsByTagName('html')->item(0)->nodeValue;

					// if the field is in the side bar
					if ($options['location'] == 'sidebar') {
						// replace height and width to make it fit in the backend
						$w = $this->getEmbedSize($options, 'width');
						$h = $this->getEmbedSize($options, 'height');

						// actual replacement
						$player = preg_replace(
							array('/width="([^"]*)"/', '/height="([^"]*)"/'),
							array("width=\"{$w}\"", "height=\"{$h}\""), $player);
					}

				} else {
					// we could not load the xml
					$player = 'Error';
				}
			}

			return $player;
		}

		public function getOEmbedXmlApiUrl($params) {
			$url = trim($params['url']);

			// trying to fix url with # in it
			// N.B. this is valid only for Youtube as other services
			// may place the resource ID elsewhere in the hash (#) tag

			// if the url contains '#' (the reel resource ID is the last part)
			if (strpos($url, '#') !== FALSE) {
				// split on every # or /
				$exploded = preg_split('/[\/#]/', $url);

				// use the last item
				$url = self::BASE_URL . $exploded[count($exploded)-1];
			}

			//var_dump($url); die;

			return 'http://www.youtube.com/oembed?format=xml&url=' . $url;
		}


		public function getIdTagName() {
			return null; // will use url as id
		}
	}