<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	define('OEMBED_PARSERS_DIR', EXTENSIONS . '/oembed_field/lib/parsers/');

	/**
	 *
	 * Abstract class that encapsulate how to parse data
	 * from oEmbedResponse
	 * @author Nicolas
	 *
	 */
	abstract class ServiceParser {
		public abstract function createArray($source, $driver, $url, &$errorFlag);
		public abstract function createXML($source, $driver, $url, &$errorFlag);
		public abstract function createJSON($source, $driver, $url, &$errorFlag);

		/**
		 *
		 * Factory method that creates a parser for the specified format
		 * @param string $format
		 * @throws ServiceParserExcpetion
		 * @return ServiceParser
		 */
		public final static function getServiceParser($format) {
			try {
				// include the parser code
				require_once(OEMBED_PARSERS_DIR . 'class.parser.' . $format . '.php');

				// get class name
				$class = 'serviceParser' . strtoupper($format);

				return new $class;

			} catch (Exception $ex) {
				throw new ServiceParserExcpetion($format);
			}
			return null;
		}
	}

	class ServiceParserExcpetion extends Exception {
		public function __construct($format) {
			$msg = vsprintf ("Error occurred when searching parser for format '%s'", $format);
			parent::__construct($message, $code);
		}
	}