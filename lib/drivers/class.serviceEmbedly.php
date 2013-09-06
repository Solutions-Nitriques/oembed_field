<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	class serviceEmbedly extends ServiceDriver {

		public function __construct() {
			// Add provider urls, http://embed.ly/embed/features/providers, in the parent::__construct below
			parent::__construct('Embed.ly', array('issuu.com','ustream.tv','vimeo.com','youtube.com','youtu.be','screenr.com','flickr.com','flic.kr','instagr.am','instagram.com','picasaweb.google.com'));
		}

		public function supportsSSL() {
			return true;
		}

		public function getOEmbedApiUrl($params) {
			$url = rawurlencode($params['url']);
			$query_params = $params['query_params'];

			return 'http://api.embed.ly/1/oembed?format=xml&url=' . $url . $query_params;
		}

		public function getIdTagName() {
			return null;
		}
	}
