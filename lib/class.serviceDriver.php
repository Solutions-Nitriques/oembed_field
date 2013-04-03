<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	// include the Service Parser master class
	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceParser.php');

	require_once(TOOLKIT . '/class.gateway.php');

	/**
	 *
	 * Abstract class that represents a service that offers oEmbed API
	 * @author Nicolas
	 *
	 */
	abstract class ServiceDriver {

		private $Name = null;

		private $Domains = null;

		/**
		 *
		 * Basic constructor that takes the name of the service and its Urls as parameters.
		 *
		 * @param string $name
		 * @param string|array $domains
		 */
		protected function __construct($name, $domains) {
			$this->Name = $name;
			$this->Domains = $domains;
		}

		/**
		 *
		 * Accessor for the Name property
		 * @return string
		 */
		public final function getName() {
			return $this->Name;
		}

		/**
		 *
		 * Accessor for the unified Domains property
		 * This will alway return an array, even if the domain was set as a string.
		 * Fix issue #19.
		 *
		 * @return array
		 */
		public final function getDomains() {
			if (!is_array($this->Domains)) {
				return array($this->Domains);
			}
			return $this->Domains;
		}

		/**
		 *
		 * Methods used to check if this drivers corresponds to the
		 * data passed in parameter. Overrides at will.
		 *
		 * @param data $url
		 * @return boolean
		 */
		public function isMatch($url) {
			$doms = $this->getDomains();
			foreach ($doms as $d) {
				if (strpos($url, $d) > -1) {
					return true;
				}
			}
			return false;
		}

		/**
		 *
		 * Gets the oEmbed data from the Driver Source, returned as an array
		 *
		 * @param array $params - parameters for the oEmbed API request
		 * @param bool $errorFlag - ref parameter to flag if the operation was successful (new in 1.3)
		 *
		 * @return array
		 * 			url => the url uses to get the data
		 * 			xml => the raw xml data
		 * 			json => the raw jason data, if any
		 * 			id => the id the resource
		 * 			dirver => the driver's name used for this resource
		 * 			title => the title of the ressource
		 * 			thumb => the thumbnail of the resource, if any
		 * 			error => the error message, if any
		 */
		public final function getDataFromSource($params, &$errorFlag) {

			// assure we have no error
			$errorFlag = false;

			// get the complete url
			$url = $this->getOEmbedApiUrl($params);

			// create the Gateway object
			$gateway = new Gateway();

			// set our url
			$gateway->init($url);

			// get the raw response, ignore errors
			$response = @$gateway->exec();

			// declare the result array
			$data = array();

			// add url to array
			$data['url'] = $url;

			// add driver to array
			$data['driver'] = $this->getName();

			// if we have a valid response
			if (!$response || strlen($response) < 1) {
				$errorFlag = true;
				$data['error'] = __('Failed to load oEmbed data');

			} else {
				// get the good parser for the service format
				// fixes Issue #15
				$parser = ServiceParser::getServiceParser($this->getAPIFormat());

				$parsedAray = @$parser->createArray($response, $this, $url, $errorFlag);

				if (!$errorFlag && $parsedAray !== FALSE) {
					// merge the parsed data
					$data = array_merge($data, $parsedAray);
				} else {
					$errorFlag = true;
					$data['error'] = __('Failed to parse oEmbed data: %s', array($parsedAray['error']));
				}

			}

			return $data;
		}

		/**
		 *
		 * Overridable method that shall return the HTML code for embedding
		 * this resource into the backend. Default implementation uses the
		 * embed code provided by the service.
		 *
		 * @param array $data
		 * @param array $options
		 */
		public function getEmbedCode($data, $options) {
			// ref to the html string to output in the backend
			$player = null;
			// xml string from the DB
			$xml_data = $data['oembed_xml'];

			if(empty($xml_data)) return false;

			// create a new DOMDocument to manipulate the XML string
			$xml = new DOMDocument();

			// if we can load the string into the document
			if (@$xml->loadXML($xml_data)) {
				// get the value of the html node
				// NOTE: this could be the XML children if the html is not encoded
				$player = $xml->getElementsByTagName('html')->item(0)->nodeValue;

				// if the field is in the side bar
				if ($options['location'] == 'sidebar') {
					// replace height and width to make it fit in the backend
					$w = $this->getEmbedSize($options, 'width');
					$h = $this->getEmbedSize($options, 'height');

					// actual replacement
					$player = preg_replace(
						array('/width="([^"]*)"/', '/height="([^"]*)"/'),
						array("width=\"{$w}\"", "height=\"{$h}\""),
						$player
					);
				}

				return $player;
			}

			return false;
		}

		/**
		 *
		 * Abstract method that shall return the URL for the oEmbed XML API
		 * @param $params
		 */
		public abstract function getOEmbedApiUrl($params);


		/**
		 *
		 * Method that returns the format used in oEmbed API responses
		 * @return string (xml|json)
		 */
		public function getAPIFormat() {
			return 'xml'; // xml || json
		}

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
		 * Method that returns the name of the Thumbnail_url tag.
		 * Overrides at will. Default returns 'title'
		 * @return string
		 */
		public function getThumbnailTagName() {
			return 'thumbnail_url';
		}

		/**
		 *
		 * Method that returns the name of the Title tag.
		 * Overrides at will. Default returns 'title'
		 * @return string
		 */
		public function getTitleTagName() {
			return 'title';
		}


		/**
		 *
		 * Overridable method that shall return the name of the tag
		 * that will be used as ID. Default returns null.
		 *
		 */
		public function getIdTagName() {
			return null; // will use url as id
		}

		/**
		 *
		 * This method will be called when adding sites
		 * to the authorized JIT image manipulations external urls.
		 *
		 * It should return domains as value
		 * i.e. array('*.example.org*', '*.example.org/images/*')
		 *
		 * NOT CURRENTLY IMPLEMENTED - FOR FUTURE USE ONLY
		 *
		 * @return array|null
		 */
		public function getNeededUrlsToJITimages() {
			return null;
		}


		/**
		 * If this method returns true, the driver support SSL embeding.
		 * Defaults to false.
		 *
		 * @since 1.6
		 *
		 * @return boolean
		 */
		public function supportsSSL() {
			return false;
		}

		/**
		 * Converts https:// and http:// to //
		 *
		 * @param string $value
		 * @return string
		 */
		private static function removeHTTPProtocol($value) {
			$value = str_replace('https://', '//', $value);
			$value = str_replace('http://', '//', $value);
			return $value;
		}

		/**
		 * This method converts data in order to support SSL embeding.
		 *
		 * @param array $data
		 * @return array - the data to be inserted in the DB
		 */
		public function convertToSSL(array &$data) {
			if ($this->supportsSSL()) {
				$data['oembed_xml'] = self::removeHTTPProtocol($data['oembed_xml']);
				$data['thumbnail_url'] = self::removeHTTPProtocol($data['thumbnail_url']);
			}
		}

		/**
		 *
		 * Utility method that returns the good size based on the location of the field.
		 *
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