<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(TOOLKIT . '/class.json.php');

	/**
	 *
	 * This class parses XML source into different other representation
	 *
	 * @since 1.7
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
			// get the data as an array
			$data = @json_decode($source, true);
			
			if ($data === FALSE) {
				$errorFlag = true;
			}
			
			if (!$errorFlag) {
				// original content
				$xml['xml'] = $source;
				
				$idTagName = $driver->getIdTagName();
				if ($idTagName == null || !isset($data[$idTagName])) {
					$xml['id'] = Lang::createHandle($url);
				} else {
					$xml['id'] = $data[$idTagName];
				}
				$xml['title'] = $data[$driver->getTitleTagName()];
				$xml['thumb'] = $data[$driver->getThumbnailTagName()];
			}

			if ($errorFlag) {
				// return error message
				$xml['error'] = __('Symphony could not parse JSON from oEmbed remote service');
			}

			return $xml;
		}

		/**
		 *
		 * Convert the $source string into a XML string
		 * @param string $source
		 * @return string
		 */
		public function createXML($source, $driver, $url, &$errorFlag) {
			// @see http://getsymphony.com/learn/api/2.3.2/toolkit/json/
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