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

			return 'http://www.youtube.com/oembed?format=xml&url=' . $url;
		}

	}