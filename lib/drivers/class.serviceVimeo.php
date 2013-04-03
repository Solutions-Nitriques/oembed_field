<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceVimeo extends ServiceDriver {

		public function __construct() {
			parent::__construct('Vimeo', 'vimeo.com');
		}

		public function getEmbedCode($data, $options) {
			return vsprintf('<iframe src="//player.vimeo.com/video/%s" width="%d" height="%d" frameborder="0"></iframe>',
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
			return 'http://vimeo.com/api/oembed.xml?url=' . trim($params['url']) . $query_params;
		}

		public function getIdTagName() {
			return 'video_id';
		}

		public function supportsSSL() {
			return true;
		}

		public function getNeededUrlsToJITimages() {
			return array(
				'http://a.vimeocdn.com/*',
				'http://b.vimeocdn.com/*',
				'http://c.vimeocdn.com/*',

				'https://a.vimeocdn.com/*',
				'https://b.vimeocdn.com/*',
				'https://c.vimeocdn.com/*',
			);
		}
	}