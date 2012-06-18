<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	// include the Service Driver master class
	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceDriver.php');

	define('OEMBED_DRIVERS_DIR', EXTENSIONS . '/oembed_field/lib/drivers/');

	/**
	 *
	 * Class that groups functionality for working with Service Drivers
	 * @author Nicolas
	 *
	 */
	final class ServiceDispatcher {

		/**
		 *
		 * Private pointer to all drivers known
		 * @var array
		 */
		private static $drivers = null;

		/**
		 *
		 * Utility function that loads all the drivers
		 * in the drivers directory
		 * @throws ServiceDriverException
		 */
		private static final function loadDrivers() {
			$load = false;

			// if the pointer is null, when sould load the drivers
			if (self::$drivers == null) {

				// create a new array
				self::$drivers = array();
				
				// get all files in the drivers folders
				$drivers = General::listStructure(OEMBED_DRIVERS_DIR, '/class.service[a-zA-Z0-9]+.php/', false, 'asc');
				
				// for each file found
				foreach ($drivers['filelist'] as $class) {
						
					$class = basename($class);
				
					try {

						// include the class code
						require_once(OEMBED_DRIVERS_DIR . $class);

						// get class name
						$class = str_replace(array('class.', '.php'), '', $class);
							
						// create new instance
						$class = new $class($url);
						
						// add the class to the stack
						self::$drivers[$class->getName()] = $class;
						
						
					} catch (Exception $ex) {
					
						throw new ServiceDriverException($url, $ex);
						
					}

				}

				// set return value
				$load = true;
				
			}
			
			return $load;
		}

		/**
		 *
		 * Public accessor for the array of all drivers
		 * @return array
		 */
		public static final function getAllDrivers() {
			// assure drivers are loaded
			self::loadDrivers();

			// return the array
			return self::$drivers;
			
		}

		/**
		 *
		 * Utility method that returns an array of the drivers' names
		 * @return array
		 */
		public static final function getAllDriversNames() {
			return array_keys(self::getAllDrivers());
		}

		/**
		 *
		 * Method that return a sub-array containing only the allowed
		 * drivers based on the $allowedList param
		 * @param string|array $allowedList allowed class names
		 */
		public static final function getAllowedDrivers($allowedList = null) {
			$allowedDrivers = array();
			if (is_array($allowedList) && count($allowedList) > 0) {
				
				$allDrivers = self::getAllDrivers();
				
				foreach ($allDrivers as $key => $driver) {
					if (array_search($key, $allowedList) !== false) {
						$allowedDrivers[$key] = $driver;
					}
				}
			}
			return $allowedDrivers;
		}

		/**
		 *
		 * Method that return a sub-array containing only the allowed
		 * drivers names based on the $allowedList param
		 * @param string|array $allowedList allowed class names
		 */
		public static final function getAllowedDriversNames($allowedList = null) {
			return array_keys(self::getAllowedDrivers($allowedList));
		}

		/**
		 *
		 * Factory method that return the good driver based on the url
		 * @param string $url
		 * @return ServiceDriver
		 * @throws ServiceDriverException
		 */
		public static final function getServiceDriver($url, $allowedList = null) {

			// no url == no driver, exit soon
			if (!$url || $url == null || strlen($url) == 0) {
				return null;
			}

			$drivers = null;

			// get the good list of drivers
			if (is_array($allowedList)) {
				$drivers = self::getAllowedDrivers($allowedList);
			} else {
				$drivers = self::getAllDrivers();
			}

			// for each driver
			foreach ($drivers as $className => $class) {

				// if it matches, return it
				if ($class->isMatch($url)) {
					return $class;
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
			$msg = vsprintf ("Error occurred when searching driver for '%s'", $url);
			if ($ex) {
				$msg = vsprintf ("Error occurred when searching driver for '%s': %s", array($url, $ex->getMessage()));
			}
			parent::__construct($msg);
		}

		public function getInnerException() {
			return $this->InnerException;
		}

	}