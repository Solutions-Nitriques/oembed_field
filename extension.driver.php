<?php
	/*
	Copyight: Solutions Nitriques 2011
	License: MIT, see the LICENCE file
	*/

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	/**
	 *
	 * Embed Videos/Image Decorator/Extension
	 * Leverage oEmbed standart in Symphony CMS (http://oembed.com/)
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
		 * 
		 * Name of the field table
		 * @var string
		 */
		private $FIELD_TBL_NAME = 'tbl_fields_oembed';

		/**
		 * Credits for the extension
		 */
		public function about() {
			return array(
				'name'			=> self::EXT_NAME,
				'version'		=> '1.0',
				'release-date'	=> '2011-07-12',
				'author'		=> array(
					'name'			=> 'Solutions Nitriques',
					'website'		=> 'http://www.nitriques.com/open-source/',
					'email'			=> 'open-source (at) nitriques.com'
				),
				'description'	=> __('Easily embed videos/images from ANY website that implements the oEmbed format (http://oembed.com/)'),
				'compatibility' => array(
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
		/*public function getSubscribedDelegates(){
			return array(
				
			);
		}*/
		
		public function install() {
			return Symphony::Database()->query("
				CREATE TABLE `$this->FIELD_TBL_NAME` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					`refresh` int(11) unsigned NOT NULL,
					`driver` varchar(150) NOT NULL
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				)
			");
		}

	}