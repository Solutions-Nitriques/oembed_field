<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class serviceVimeo extends ServiceDriver {

		public function __construct() {
			parent::__construct('Vimeo', 'vimeo.com');
		}

		public function getEmbedCode($data) {

		}

		public function getOEmbedApiUrl($params) {

		}


	}