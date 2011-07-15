<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	abstract class ServiceDriver {

		protected $Name = '';

		protected $Domain = '';

		protected function __construct($name, $domain) {
			$this->Name = $name;
			$this->Domain = $domain;
		}
		
		function exception_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
			//if (!(error_reporting() & $errno)) {
		        // Ce code d'erreur n'est pas inclus dans error_reporting()
		        //return;
		    //}
			
		    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
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

			try {
				set_error_handler( array($this, 'exception_error_handler') );
				
				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = false;
				$doc->load($url);

				$xml['xml'] = $doc->saveXML();
				$xml['url'] = $url;
				$xml['title'] = $doc->getElementsByTagName($this->getTitleTagName())->item(0)->nodeValue;
				$xml['thumb'] = $doc->getElementsByTagName($this->getThumbnailTagName())->item(0)->nodeValue;
				
			} catch (Exception $ex) {

				$xml['error'] = $ex->getMessage();
			} 
				
			restore_error_handler();

			return $xml;
		}

		public abstract function getEmbedCode($data);

		public abstract function getOEmbedXmlApiUrl($params);

		public function getTitleTagName() {
			return 'title';
		}
		
		public function getThumbnailTagName() {
			return 'thumbnail_url';
		}
	}