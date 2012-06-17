<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceSlideShare extends ServiceDriver {

		public function __construct() {
			parent::__construct('SlideShare', 'slideshare.net');
		}


		public function getEmbedCode($data, $options) {
			return vsprintf('<iframe src="http://www.slideshare.net/slideshow/embed_code/%s" width="%d" height="%d" frameborder="0"></iframe>',
							array(	$data['res_id'],
									$this->getEmbedSize($options, 'width'),
									$this->getEmbedSize($options, 'height')
								  )
					);
		}

		public function getOEmbedApiUrl($params) {
			// DO NOT CONCAT WITH + IN PHP ... USE .
			// TABARNAK !!!
			$query_params = $params['query_params'];
			return 'http://www.slideshare.net/api/oembed/2?format=xml&url=' . trim($params['url']) . $query_params;
			var_dump($params);die;
		}

		public function getIdTagName() {
			return 'slideshow-id';
		}

		public function getNeededUrlsToJITimages() {
			return array();
		}
	}