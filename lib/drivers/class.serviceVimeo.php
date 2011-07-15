<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceVimeo extends ServiceDriver {

		public function __construct() {
			parent::__construct('Vimeo', 'vimeo.com');
		}

		public function getEmbedCode($data) {
			return vsprintf('<iframe src="http://player.vimeo.com/video/%s" width="%d" height="%d" frameborder="0"></iframe>',
							array($data['id'], $data['width'], $data['height']));
		}

		public function getOEmbedXmlApiUrl($params) {
			// DO NOT CONCAT WITH + IN PHP ... USE .
			// TABARNAK !!!
			return 'http://vimeo.com/api/oembed.xml?url=' . $params['url'];
		}


	}