<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	abstract class ServiceDriver {

		protected $Name = '';

		protected $Domain = '';

		protected function __construct($name, $domain) {
			$this->Name = $name;
			$this->Domain = $domain;
		}

		public function getName() {
			return $this->Name;
		}

		public function getDomain() {
			return $this->Domain;
		}

		public function isMatch($url) {
			return strpos($url, $this->Domain) > -1;
		}

		public abstract function getEmbedCode($data);

		public abstract function getOEmbedApiUrl($params);


	}