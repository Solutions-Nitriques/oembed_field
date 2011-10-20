<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceVimeo extends ServiceDriver {

		public function __construct() {
			parent::__construct('Vimeo', 'vimeo.com');
		}

		public function about() {
			return array(
				'name'			=> $this->Name,
				'version'		=> '1.1',
				'release-date'	=> '2011-10-19',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				)
	 		);
		}

		public function getEmbedCode($data, $options) {
			return vsprintf('<iframe src="http://player.vimeo.com/video/%s" width="%d" height="%d" frameborder="0"></iframe>',
							array(	$data['res_id'],
									$this->getEmbedSize($options, 'width'),
									$this->getEmbedSize($options, 'height')
								  )
					);
		}

		public function getOEmbedXmlApiUrl($params) {
			// DO NOT CONCAT WITH + IN PHP ... USE .
			// TABARNAK !!!
			return 'http://vimeo.com/api/oembed.xml?url=' . $params['url'];
		}


		public function getIdTagName() {
			return 'video_id';
		}

		public function getNeededUrlsToJITimages() {
			return array(
				'http://a.vimeocdn.com/*',
				'http://b.vimeocdn.com/*',
				'http://c.vimeocdn.com/*'
			);
		}
	}