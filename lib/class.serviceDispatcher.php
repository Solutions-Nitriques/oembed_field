<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');


	class ServiceDispatcher {

		static private $_registeredClasses = array(
			//'ServiceYouTube',
			'ServiceVimeo',
			//'ServiceFlickr'
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
		static function getServiceDriver($url) {
			foreach (self::$_registeredClasses as $class) {
				if ($class::isMatch($url)) {
					return new $class($url);
				}
			}

			throw new ServiceDriverNotFoundException($url);
		}

	}

	class ServiceDriverNotFoundException extends Exception {

		private $InnerException = null;

		public function __construct($url, Exception $ex = null) {
			$this->InnerException = $ex;
			parent::__construct(__("No ServiceDriver found for '%s'.", $url));
		}

		public function getInnerException() {
			return $this->InnerException;
		}

	}