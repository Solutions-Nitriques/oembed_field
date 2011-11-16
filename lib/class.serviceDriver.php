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
		public final function getName() {
			return $this->Name;
		}

		/**
		 *
		 * Accessor for the Domain property
		 */
		public final function getDomain() {
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
		 *
		 * @param array $data
		 * @param bool $errorFlag - ref parameter to flag if the operation was successful (new in 1.3)
		 */
		public final function getXmlDataFromSource($data, &$errorFlag) {

			$url = $this->getOEmbedXmlApiUrl($data);

			$xml = array();

			// add url to array
			$xml['url'] = $url;

			// trying to load XML into DOM Document
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = false;

			// ignore errors, but save if it was successful
			$errorFlag = !(@$doc->load($url));

			if (!$errorFlag) {
				$xml['xml'] = $doc->saveXML();

				// add id to array
				$idTagName = $this->getIdTagName();
				if ($idTagName == null) {
					$xml['id'] = Lang::createHandle($url);
				} else {
					$xml['id'] = $doc->getElementsByTagName($idTagName)->item(0)->nodeValue;
				}
				
				$xml['title'] = $doc->getElementsByTagName($this->getTitleTagName())->item(0)->nodeValue;
				$xml['thumb'] = $doc->getElementsByTagName($this->getThumbnailTagName())->item(0)->nodeValue;
				
			}
			else {
				// return somthing since the column can't be null
				$xml['xml'] = '<error>' . __('Symphony could not load XML from oEmbed remote service') . '</error>';
			}

			return $xml;
		}

		/**
		 *
		 * Abstract method that shall return the HTML code for embedding
		 * this resource into the backend
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
		 * Method that returns the name of the root tag.
		 * Overrides at will. Default returns 'oembed'
		 * @return string
		 */
		public function getRootTagName() {
			return 'oembed';
		}

		/**
		 *
		 * Abstract method that shall return the name of the tag that will be used as ID.
		 *
		 * N.B: Can return null: Id will be a handle created from the url
		 */
		public abstract function getIdTagName();

		/**
		 *
		 * This method will be called when adding sites
		 * to the authorized JIT image manipulations external urls.
		 *
		 * It should return url as value
		 * i.e. array('http://*.example.org', 'http://*.example.org')
		 *
		 * @return array|null
		 */
		public function getNeededUrlsToJITimages() {
			return null;
		}

		/**
		 *
		 * Utility method that returns the good size based on the location of the field
		 * @param array $options
		 * @param string $size (width and/or height)
		 * @return array
		 */
		protected function getEmbedSize($options, $size) {
			if (!isset($options['location']) || !isset($options[$size . '_side']) || $options['location'] == 'main' ) {
				return $options[$size];
			}
			return $options[$size. '_side'];
		}

	}