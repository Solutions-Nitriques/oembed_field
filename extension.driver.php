<?php
	/*
	Copyight: Solutions Nitriques 2011
	License: MIT, see the LICENCE file
	*/

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	require_once(EXTENSIONS . '/oembed_field/fields/field.oembed.php');


	/**
	 *
	 * Embed Videos/Image Decorator/Extension
	 * Leverage oEmbed standard in Symphony CMS (http://oembed.com/)
	 * @author nicolasbrassard
	 *
	 */
	class extension_oembed_field extends Extension {

		/**
		 * Name of the extension
		 * @var string
		 */
		const EXT_NAME = 'Field: oEmbed';

		/**
		 * Credits for the extension
		 */
		public function about() {
			return array(
				'name'			=> self::EXT_NAME,
				'version'		=> '1.3.1',
				'release-date'	=> '2011-10-xx',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				),
				'description'	=> __('Easily embed videos/images from ANY website that implements the oEmbed format (http://oembed.com/)'),
				'compatibility' => array(
					'2.2.3' => true,
					'2.2.2' => true,
					'2.2.1' => true,
					'2.2' => true
				)
	 		);
		}

		/**
		 *
		 * Symphony utility function that permits to
		 * implement the Observer/Observable pattern.
		 * We register here delegate that will be fired by Symphony
		 */
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendJS'
				)
			);
		}

		/**
		 *
		 * Appends javascript file referneces into the head, if needed
		 * @param array $context
		 */
		public function appendJS(Array $context) {
			// store de callback array localy
			$c = Administration::instance()->getPageCallback();

			// publish page, new or edit
			if(isset($c['context']['section_handle']) && in_array($c['context']['page'], array('new', 'edit'))){

				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/oembed_field/assets/publish.oembed.js',
					time(),
					false
				);

				return;
			}

			// section page, new or edit
			if($c['driver'] == 'blueprintssections') {

				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/oembed_field/assets/section.oembed.js',
					time(),
					false
				);

				Administration::instance()->Page->addStylesheetToHead(
					URL . '/extensions/oembed_field/assets/section.oembed.css',
					'screen',
					time() + 1,
					false
				);

				return;
			}
		}


		/* ********* INSTALL/UPDATE/UNISTALL ******* */

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function install() {
			$create = FieldOembed::createFieldTable();

			$unique = FieldOembed::updateFieldTable_Unique();

			$params = true; //FieldOembed::createParamsSetTable();

			return $create && $unique && $params;
		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function update($previousVersion) {
			$ret = true;

			// are we updating from lower than 1.3.1 ?
			if (version_compare($previousVersion,'1.3.1') == -1) {
				// update for unique setting
				$ret_unique = FieldOembed::updateFieldTable_Unique();

				// set the return value
				$ret = $ret_unique;
			}

			// are we updating from lower or equal than 1.3.1 ?
			/*if (version_compare($previousVersion, '1.3.1') <= 0) {
				// create the table needed for params set
				$ret_params = FieldOembed::createParamsSetTable();

				$ret = $ret & $ret_params;
			}*/

			return $ret;
		}

		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public function uninstall() {
			$params = FieldOembed::deleteParamsSetTable();

			return $params && FieldOembed::deleteFieldTable();
		}

	}