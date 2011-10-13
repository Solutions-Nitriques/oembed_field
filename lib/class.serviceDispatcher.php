<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceDriver.php');

	/**
	 *
	 * Class that groups functionality for working with Service Drivers
	 * @author Nicolas
	 *
	 */
	class ServiceDispatcher {

		protected static $drivers = null;

		/**
		 *
		 * Factory method that return the good driver based on the url
		 * @param string $url
		 * @return ServiceDriver
		 * @throws ServiceDriverException
		 */
		public static function getServiceDriver($url) {

			if (!$url || $url == null || strlen($url) == 0) {
				return null;
			}

			$dir = EXTENSIONS . '/oembed_field/lib/drivers/';

			$drivers = General::listStructure($dir, null, false, true);

			foreach ($drivers['filelist'] as $class) {

				try {

					require_once($dir . $class);

					// get class name
					$class = str_replace(array('class.', '.php'), '', $class);

					// create new instance
					$class = new $class($url);

					// if it matches, return it
					if ($class->isMatch($url)) {
						return $class;
					}

				} catch (Exception $ex) {
					throw new ServiceDriverException($url, $ex);
				}

			}

			// not found
			return null;
		}

	}

	/**
	 *
	 * Exception class that wraps around another exception
	 * @author Nicolas
	 *
	 */
	class ServiceDriverException extends Exception {

		private $InnerException = null;

		public function __construct($url, Exception $ex = null) {
			$this->InnerException = $ex;
			$msg = vsprintf ("Error occured when searching driver for '%s'", $url);
			if ($ex) {
				$msg = vsprintf ("Error occured when searching driver for '%s': %s", array($url, $ex->getMessage()));
			}
			parent::__construct($msg);
		}

		public function getInnerException() {
			return $this->InnerException;
		}

	}