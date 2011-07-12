<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceDriver.php');

	class ServiceDispatcher {

		static private $_registeredClasses = array(
			//'serviceYouTube',
			'serviceVimeo',
			//'serviceFlickr'
		);

		private $_driver = null;

		public function __construct($url) {
			$this->_driver = self::getServiceDriver($url);
		}

		/**
		 *
		 * @return ServiceDriver
		 */
		public function getDriver() {
			return $this->_driver;
		}

		/**
		 *
		 * Factory method that return the good driver based on the url
		 * @param string $url
		 * @return ServiceDriver
		 * @throws ServiceDriverNotFoundException
		 */
		public static function getServiceDriver($url) {

			if (!$url || $url == null || strlen($url) == 0) {
				return;
			}

			foreach (self::$_registeredClasses as $class) {

				try {

					require_once(EXTENSIONS . "/oembed_field/lib/drivers/class.$class.php");

					$class = new $class($url);

					if ($class->isMatch($url)) {
						return $class;
					}

				} catch (Exception $ex) {
					throw new ServiceDriverNotFoundException($url, $ex);
				}

			}

			throw new ServiceDriverNotFoundException($url);
		}

	}

	class ServiceDriverNotFoundException extends Exception {

		private $InnerException = null;

		public function __construct($url, Exception $ex = null) {
			$this->InnerException = $ex;
			parent::__construct( vsprintf ("No ServiceDriver found for '%s'.", $url));
		}

		public function getInnerException() {
			return $this->InnerException;
		}

	}