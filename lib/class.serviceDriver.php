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

		public function getXmlDataFromSource($data) {
			$url = $this->getOEmbedXmlApiUrl($data);

			$xml = array();

			//try {
				$doc = new DOMDocument();
				$doc->loadXML($url);

				$xml['xml'] = $doc->saveXML();
				$xml['url'] = $url;
				$xml['title'] = $doc->getElementsByTagName($this->getTitleTagName())->item(0)->nodeValue;

			//} catch (Exception $ex) {

				$xml['error'] = $ex->getMessage();
			//}

			return $xml;
		}

		public abstract function getEmbedCode($data);

		public abstract function getOEmbedXmlApiUrl($params);

		public function getTitleTagName() {
			return 'title';
		}
	}