<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	/**
	 *
	 * Abstract class that represents a service that offers oEmbed API
	 * @author Nicolas
	 *
	 */
	abstract class ServiceDriver {

		protected $Name = '';

		protected $Domain = '';

		/**
		 *
		 * Basic constructor that takes the name of the service and its Url as parameters
		 * @param string $name
		 * @param string $domain
		 */
		protected function __construct($name, $domain) {
			$this->Name = $name;
			$this->Domain = $domain;
		}

		/**
		 *
		 * Accessor for the Name property
		 */
		public function getName() {
			return $this->Name;
		}

		/**
		 *
		 * Accessor for the Domaine property
		 */
		public function getDomain() {
			return $this->Domain;
		}

		/**
		 *
		 * Methods used to check if this drivers corresponds to the
		 * data passed in parameter. Overrides at will
		 * @param data $url
		 */
		public function isMatch($url) {
			return strpos($url, $this->Domain) > -1;
		}

		/**
		 *
		 * Gets the oEmbed XML data from the Driver Source
		 * @param array $data
		 */
		public function getXmlDataFromSource($data) {

			$url = $this->getOEmbedXmlApiUrl($data);

			$xml = array();

			//try {
				//set_error_handler( array($this, 'exception_error_handler') );

				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = false;
				$doc->load($url);

				$xml['xml'] = $doc->saveXML();
				$xml['url'] = $url;
				$xml['title'] = $doc->getElementsByTagName($this->getTitleTagName())->item(0)->nodeValue;
				$xml['thumb'] = $doc->getElementsByTagName($this->getThumbnailTagName())->item(0)->nodeValue;

				$idTagName = $this->getIdTagName();
				if ($idTagName == null) {
					$xml['id'] = Lang::createHandle($url);
				} else {
					$xml['id'] = $doc->getElementsByTagName($idTagName)->item(0)->nodeValue;
				}

			/*} catch (Exception $ex) {

				$xml['error'] = $ex->getMessage();
			} */

			//restore_error_handler();

			return $xml;
		}

		/**
		 *
		 * Abstract method that shall return the HTML code for embeding
		 * this ressource into the backend
		 * @param array $data
		 * @param array $options
		 */
		public abstract function getEmbedCode($data, $options);

		/**
		 *
		 * Abstract method that shall return the URL for the oEmbed XML API
		 * @param $params
		 */
		public abstract function getOEmbedXmlApiUrl($params);

		/**
		 *
		 * Basic about method that returns an array for the credits of the driver
		 */
		public abstract function about();

		/**
		 *
		 * Method that returns the name of the Title tag.
		 * Overrides at will. Default returns 'title'
		 */
		public function getTitleTagName() {
			return 'title';
		}

		/**
		 *
		 * Method that returns the name of the Thumbnail tag.
		 * Overrides at will. Default returns 'thumbnail_url'
		 */
		public function getThumbnailTagName() {
			return 'thumbnail_url';
		}

		/**
		 *
		 * Abstract method that shall return the name of the tag that will be used as ID.
		 *
		 * N.B: Can return null: Id will be a handle created from the url
		 */
		public abstract function getIdTagName();


		function exception_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
			//if (!(error_reporting() & $errno)) {
		        // Ce code d'erreur n'est pas inclus dans error_reporting()
		        //return;
		    //}

		    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
		}
	}