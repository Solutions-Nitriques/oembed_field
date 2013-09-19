<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	/**
	 *
	 * This class parses XML source into different other representation
	 * @author Nicolas
	 *
	 */
	class serviceParserXML extends ServiceParser {

		/**
		 *
		 * Convert the $source string into a array.
		 * This array should always respect the scheme defined by the
		 * getDataFromSource method of the ServiceDriver
		 * @param string $source
		 * @return string
		 */
		public function createArray($source, $driver, $url, &$errorFlag) {
			// trying to load XML into DOM Document
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = false;

			// ignore errors, but save it if was successful
			$errorFlag = !(@$doc->loadXML($source));

			if (!$errorFlag) {
				$xml['xml'] = @$doc->saveXML();

				if ($xml['xml'] === FALSE) {
					$errorFlag = true;
				}
				else {
					// add id to array
					$idTagName = $driver->getIdTagName();
					if ($idTagName == null) {
						$xml['id'] = Lang::createHandle($url);
					} else {
						$xml['id'] = $doc->getElementsByTagName($idTagName)->item(0)->nodeValue;
					}
					$xml['title'] = $doc->getElementsByTagName($driver->getTitleTagName())->item(0)->nodeValue;
					$xml['thumb'] = $doc->getElementsByTagName($driver->getThumbnailTagName())->item(0)->nodeValue;
				}
			}

			if ($errorFlag) {
				// return error message
				$xml['error'] = __('Symphony could not parse XML from oEmbed remote service');
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
			// we do not have to do anything here since we already have xml
			return $source;
		}

		/**
		 *
		 * Convert the $source string into a JSON string
		 * @param string $source
		 * @return string
		 */
		public function createJSON($source, $driver, $url, &$errorFlag) {
			return json_encode($source);
		}

	}