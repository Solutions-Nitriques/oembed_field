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
	 * @author nicolas
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
				'version'		=> '1.4',
				'release-date'	=> '2012-04-xx',
				'author'		=> array(
					array (
						'name'			=> 'Solutions Nitriques',
						'website'		=> 'http://www.nitriques.com/open-source/',
						'email'			=> 'open-source (at) nitriques.com' ),
					array (
						'name'			=> 'Deux Huit Huit',
						'website'		=> 'http://www.deuxhuithuit.com',
						'email'			=> 'open-source (at) deuxhuithuit.com' )
				),
				'description'	=> __('Easily embed videos/images from ANY website that implements the oEmbed format (http://oembed.com/)'),
				'compatibility' => array(
					'2.2.5' => true,
					'2.2.4' => true,
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

				/*Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/oembed_field/assets/section.oembed.js',
					time(),
					false
				);*/

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
			// pre v1.3.1
			$create = FieldOembed::createFieldTable();

			// v1.3.1
			$unique = FieldOembed::updateFieldTable_Unique();

			// v1.3.2
			$thumbs = FieldOembed::updateFieldTable_Thumbs();

			// v1.3.2 - abandoned
			// $params = FieldOembed::createParamsSetTable();
			// $params_set_id = FieldOembed::updateFieldTable_ParamsSetId();
			
			// v1.4
			$params = FieldOembed::updateFieldTable_QueryParams();
			$driver = FieldOembed::updateFieldTable_Driver();

			return $create && $unique && $thumbs && $params && $driver;

		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function update($previousVersion) {
			$ret = true;

			// are we updating from lower than 1.3.1 ?
			if ($ret && version_compare($previousVersion,'1.3.1') == -1) {
				// update for unique setting
				$ret_unique = FieldOembed::updateFieldTable_Unique();

				// set the return value
				$ret = $ret_unique;
			}

			// are we updating from lower than 1.3.2 ?
			if ($ret && version_compare($previousVersion, '1.3.2') == -1) {
				
				// update for the thumbs settings
				$ret_thumbs = FieldOembed::updateFieldTable_Thumbs();
				
				// create the table needed for params set
				//$ret_params = FieldOembed::createParamsSetTable();
				// update for the params set id settings
				//$ret_par_sid = FieldOembed::updateFieldTable_ParamsSetId();
				
				
				// v1.4
				// update for the params settings
				$ret_params = FieldOembed::updateFieldTable_QueryParams();
				
				// update for the driver column && set all drivers as allowed by default
				$ret_driver = FieldOembed::updateFieldTable_Driver() && FieldOembed::updateFieldData_Driver();

				// set the return value
				$ret = $ret_thumbs && $ret_params && $ret_driver;
			}

			return $ret;
		}

		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public function uninstall() {
			// v1.3.2
			//$params = FieldOembed::deleteParamsSetTable();

			// pre v1.3.2
			$field = FieldOembed::deleteFieldTable();

			return $field;
		}

	}