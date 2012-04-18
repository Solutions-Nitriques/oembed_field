<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceYouTube extends ServiceDriver {

		const BASE_URL = "http://youtu.be/";

		public function __construct() {
			parent::__construct('YouTube', array('youtube.com', 'youtu.be')); // Fix Issue #19
		}

		public function about() {
			return array(
				'name'			=> $this->getName(),
				'version'		=> '1.3',
				'release-date'	=> '2012-04-17',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				),
				array(
					'name'			=> 'Deux Huit Huit',
					'website'		=> 'http://www.deuxhuithuit.com',
					'email'			=> 'open-source (at) deuxhuithuit.com'
				)
	 		);
		}

		public function getOEmbedApiUrl($params) {
			$url = trim($params['url']);
			$query_params = $params['query_params'];

			// trying to fix url with # in it
			// N.B. this is valid only for Youtube as other services
			// may place the resource ID elsewhere in the hash (#) tag

			// if the url contains '#' (the real resource ID is the last part)
			if (strpos($url, '#') !== FALSE) {
				// split on every # or /
				$exploded = preg_split('/[\/#]/', $url);

				// use the last item
				$url = self::BASE_URL . $exploded[count($exploded)-1];
			}

			return 'http://www.youtube.com/oembed?format=xml&url=' . $url . $query_params;
		}

	}