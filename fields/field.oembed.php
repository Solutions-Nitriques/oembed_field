<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceDispatcher.php');

	/**
	 *
	 * Field class that will represent an oEmbed ressource
	 * @author Nicolas
	 *
	 * Based on @nickdunn's Vimeo field: https://github.com/nickdunn/vimeo_videos/blob/master/fields/field.vimeo_video.php
	 *
	 */
	class FieldOembed extends Field {

		/**
		 *
		 * Name of the field table
		 * @var string
		 */
		private $FIELD_TBL_NAME = 'tbl_fields_oembed';

		public function __construct(&$parent){
			parent::__construct($parent);
			$this->_name = __('oEmbed Ressource Field');
			$this->_required = false;
			$this->set('required', 'no');

			//var_dump($this->get());
			//die;

			//$sd = new ServiceDispatcher('http://vimeo.com');

			//var_dump($sd->getDriver());
		}

		function isSortable(){
			return false;
		}

		function canFilter(){
			return false;
		}

		public function canImport(){
			return false;
		}

		public function canPrePopulate(){
			return false;
		}

		public function mustBeUnique(){
			return false;
		}

		public function allowDatasourceOutputGrouping(){
			return false;
		}

		public function requiresSQLGrouping(){
			return false;
		}

		public function allowDatasourceParamOutput(){
			return false;
		}

		/**
		 *
		 * Validates input
		 * @param $data
		 * @param $message
		 * @param $entry_id
		 */
		function checkPostFieldData($data, &$message, $entry_id=NULL){

			$message = NULL;

			if (empty($data)) {
				return self::__OK__;
			}

			/*$clip_id = VimeoHelper::getClipId($data);

			if (!is_numeric($clip_id)) {
				$message = __("%s must be a valid Vimeo clip ID or video URL", array($this->get('label')));
				return self::__INVALID_FIELDS__;
			}

			$clip_xml = VimeoHelper::getClipXML($clip_id);
			if (!$clip_xml) {
				$message = "Failed to load clip XML";
				return self::__INVALID_FIELDS__;
			}*/

			return self::__OK__;
		}


		public function processRawFieldData($data, &$status, $simulate = false, $entry_id = null) {

			if (trim($data) == '') return array();

			$status = self::__OK__;

			//$result = VimeoHelper::getClipInfo(VimeoHelper::getClipId($data));

			// HACK: couldn't figure out how to validate in checkPostFieldData() and then prevent
			// this processRawFieldData function executing, since it requires valid data to load the XML
			/*if (!is_array($result)) {
				$message = "Failed to load clip XML";
				$status = self::__MISSING_FIELDS__;
				return;
			}
			*/

			return $result;
		}

		/**
		 * Appends data into the XML tree
		 * @param $wrapper
		 * @param $data
		 */
		function appendFormattedElement(&$wrapper, $data){

			if(!is_array($data) || empty($data)) return;

			// If cache has expired refresh the data array from parsing the API XML
			/*if ((time() - $data['last_updated']) > ($this->_fields['refresh'] * 60)) {
				$data = VimeoHelper::updateClipInfo($data['clip_id'], $this->_fields['id'], $wrapper->getAttribute('id'), $this->Database);
			}

			$video = new XMLElement($this->get('element_name'));

			$video->setAttributeArray(array(
				'clip-id' => $data['clip_id'],
				'width' => $data['width'],
				'height' => $data['height'],
				'duration' => $data['duration'],
				'plays' => $data['plays'],
			));

			$video->appendChild(new XMLElement('title', General::sanitize($data['title'])));
			$video->appendChild(new XMLElement('caption', General::sanitize($data['caption'])));

			$user = new XMLElement('user');
			$user->appendChild(new XMLElement('name', $data['user_name']));
			$user->appendChild(new XMLElement('url', $data['user_url']));

			$thumbnail = new XMLElement('thumbnail');
			$thumbnail->setAttributeArray(array(
				'width' => $data['thumbnail_width'],
				'height' => $data['thumbnail_height'],
				'size' => 'large',
			));
			$thumbnail->appendChild(new XMLElement('url', $data['thumbnail_url']));
			$video->appendChild($thumbnail);

			$thumbnail = new XMLElement('thumbnail');
			$thumbnail->setAttributeArray(array(
				'width' => $data['thumbnail_medium_width'],
				'height' => $data['thumbnail_medium_height'],
				'size' => 'medium',
			));
			$thumbnail->appendChild(new XMLElement('url', $data['thumbnail_medium_url']));
			$video->appendChild($thumbnail);

			$thumbnail = new XMLElement('thumbnail');
			$thumbnail->setAttributeArray(array(
				'width' => $data['thumbnail_small_width'],
				'height' => $data['thumbnail_small_height'],
				'size' => 'small',
			));
			$thumbnail->appendChild(new XMLElement('url', $data['thumbnail_small_url']));
			$video->appendChild($thumbnail);

			$video->appendChild($user);

			$wrapper->appendChild($video);*/
		}

		function commit(){
			if(!parent::commit()) return false;

			/*$id = $this->get('id');
			$refresh = $this->get('refresh');

			if($id === false) return false;

			$fields = array();

			$fields['field_id'] = $id;
			$fields['refresh'] = $refresh;

			$this->_engine->Database->query("DELETE FROM `tbl_fields_".$this->handle()."` WHERE `field_id` = '$id' LIMIT 1");
			return $this->_engine->Database->insert($fields, 'tbl_fields_' . $this->handle());
			*/

		}

		public function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){

			$value = General::sanitize($data['url']);
			$label = Widget::Label($this->get('label'));

			$url = new XMLElement('input');
			$url->setAttribute('type', 'text');
			$url->setAttribute('name', 'fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix);
			$url->setAttribute('value', $value);

			if (strlen($value) == 0 || $flagWithError != NULL) {

				if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));

			} else {

				$url->setAttribute('class', 'hidden');

				$video_container = new XMLElement('span');
				$video_container->setAttribute('class', 'frame');



				$change = new XMLElement('a', 'Remove Video');
				$change->setAttribute('class', 'change');
				$video->appendChild($change);

				$video_container->appendChild($video);
				$label->appendChild($video_container);

			}

			$label->appendChild($clip_id);

			if($flagWithError != NULL) $wrapper->appendChild(Widget::wrapFormElementWithError($label, $flagWithError));
			else $wrapper->appendChild($label);*/
		}


		function prepareTableValue($data, XMLElement $link=NULL){
			/*if(strlen($data['clip_id']) == 0) return NULL;

			$image = '<img src="' . URL . '/image/2/75/75/5/1/' . str_replace('http://', '', $data['thumbnail_url']) .'" alt="' . $data['title'] .'" width="75" height="75"/>';

			if($link){
				$link->setValue($image);
				return $link->generate();
			}

			else{
				$link = new XMLElement('span', $image . '<br />' . $data['plays'] . ' plays');
				return $link->generate();
			}
			*/
		}

		public function preparePlainTextValue($data, $entry_id = null) {
			return (
				isset($data['title'])
					? $data['title']
					: $data['url']
			);
		}

		/**
		 *
		 * Creates table needed for entries of invidual fields
		 */
		function createTable(){
			return Symphony::Database()->query(
				"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`url` varchar(2048) unsigned NOT NULL,
				`url_oembed_xml` varchar(2048) unsigned NOT NULL,
				`title` varchar(2048) default NULL,
				`oembed_xml` text,
				`dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
				PRIMARY KEY  (`id`),
				);"
			);
		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public static function createFieldTable() {
			return Symphony::Database()->query("
				CREATE TABLE `$this->FIELD_TBL_NAME` IF NOT EXISTS (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					`refresh` int(11) unsigned NOT NULL,
					`driver` varchar(150) NOT NULL
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				)
			");
		}


		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public static function deleteFieldTable() {
			return Symphony::Database()->query("
				DROP TABLE `$this->FIELD_TBL_NAME`
					IF EXISTS
			");
		}

		public function displaySettingsPanel(&$wrapper, $errors=NULL){
			parent::displaySettingsPanel($wrapper, $errors);
			$this->appendRequiredCheckbox($wrapper);
			$this->appendShowColumnCheckbox($wrapper);

			/*$label = Widget::Label('Update cache (minutes; leave blank to never update) <i>Optional</i>');
			$label->appendChild(Widget::Input('fields['.$this->get('sortorder').'][refresh]', $this->get('refresh')));
			$wrapper->appendChild($label);
			*/
		}
	}