<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceDailymotion extends ServiceDriver {

		public function __construct() {
			parent::__construct('Dailymotion', 'dailymotion.com');
		}

		public function getNeededUrlsToJITimages() {
			return array(
			
				'static2.dmcdn.net/*'

			);
		}
		
		public function getOEmbedApiUrl($params) {
			$url = trim($params['url']);
			$query_params = $params['query_params'];

			return 'http://www.dailymotion.com/services/oembed?format=xml&url=' . $url . $query_params;
		}

	}