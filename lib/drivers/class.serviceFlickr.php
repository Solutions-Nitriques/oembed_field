<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceFlickr extends ServiceDriver {

		public function __construct() {
			parent::__construct('Flickr', 'flickr.com');
		}

		public function getEmbedCode($data, $options) {
			return vsprintf('<img src="%s" width="%d" height="%d" alt="%s" />',
							array(	$data['res_id'],
									$this->getEmbedSize($options, 'width'),
									$this->getEmbedSize($options, 'height'),
									General::sanitize($data['title'])
								  )
							);
		}

		public function getOEmbedApiUrl($params) {
			$query_params = $params['query_params'];
			return 'http://www.flickr.com/services/oembed?url=' . $params['url']. $query_params;
		}

		public function getIdTagName() {
			return 'url';
		}

		public function getNeededUrlsToJITimages() {
			return array(
				'http://www.flickr.com/*',
				'http://farm1.static.flickr.com/*',
				'http://farm2.static.flickr.com/*',
				'http://farm3.static.flickr.com/*',
				'http://farm4.static.flickr.com/*',
				'http://farm5.static.flickr.com/*',
				'http://farm6.static.flickr.com/*',
				'http://farm7.static.flickr.com/*',
				'http://farm8.static.flickr.com/*',
				'http://farm9.static.flickr.com/*',
				'http://farm10.static.flickr.com/*',
				'http://farm1.staticflickr.com/*',
				'http://farm2.staticflickr.com/*',
				'http://farm3.staticflickr.com/*',
				'http://farm4.staticflickr.com/*',
				'http://farm5.staticflickr.com/*',
				'http://farm6.staticflickr.com/*',
				'http://farm7.staticflickr.com/*',
				'http://farm8.staticflickr.com/*',
				'http://farm9.staticflickr.com/*',
				'http://farm10.staticflickr.com/*'
			);
		}
	}