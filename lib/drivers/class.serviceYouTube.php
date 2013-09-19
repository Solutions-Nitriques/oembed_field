<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceYouTube extends ServiceDriver {

		const BASE_URL = "http://youtu.be/";

		public function __construct() {
			parent::__construct('YouTube', array('youtube.com', 'youtu.be')); // Fix Issue #19
		}

		public function supportsSSL() {
			return true;
		}

		public function getNeededUrlsToJITimages() {
			return array(

				'http://i1.ytimg.com/*',
				'http://i2.ytimg.com/*',
				'http://i3.ytimg.com/*',
				'http://i4.ytimg.com/*',
				'http://i5.ytimg.com/*',

				'https://i1.ytimg.com/*',
				'https://i2.ytimg.com/*',
				'https://i3.ytimg.com/*',
				'https://i4.ytimg.com/*',
				'https://i5.ytimg.com/*'

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