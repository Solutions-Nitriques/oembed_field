<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	/**
	 *
	 * This class parses XML source into different other representation
	 * @author Nicolas
	 *
	 */
	class serviceParserJSON extends ServiceParser {

		/**
		 *
		 * Convert the $source string into a array.
		 * This array should always respect the scheme defined by the
		 * getDataFromSource method of the ServiceDriver
		 * @param string $source
		 * @return string
		 */
		public function createArray($source, $driver, $url, &$errorFlag) {

		}

		/**
		 *
		 * Convert the $source string into a XML string
		 * @param string $source
		 * @return string
		 */
		public function createXML($source, $driver, $url, &$errorFlag) {
			// @see http://getsymphony.com/learn/api/2.3.1/toolkit/json/
			return JSON::convertToXML($source);
		}

		/**
		 *
		 * Convert the $source string into a JSON string
		 * @param string $source
		 * @return string
		 */
		public function createJSON($source, $driver, $url, &$errorFlag) {
			// we do not have to do anything here since we already have JSON
			return $source;
		}

	}