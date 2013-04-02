<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(TOOLKIT . '/class.field.php');
	require_once(EXTENSIONS . '/oembed_field/lib/class.serviceDispatcher.php');

	/**
	 *
	 * Field class that will represent an oEmbed resource
	 * @author Nicolas
	 *
	 * Based on @nickdunn's Vimeo field: https://github.com/nickdunn/vimeo_videos/
	 *
	 */
	class FieldOembed extends Field {

		/**
		 *
		 * Name of the field table
		 * @var string
		 */
		const FIELD_TBL_NAME = 'tbl_fields_oembed';

		/**
		 *
		 * Name of the parameters set table
		 * @var string
		 */
		const FIELD_PS_TBL_NAME = 'tbl_fields_oembed_param_sets';

		/**
		 *
		 * Constructor for the oEmbed Field object
		 * @param mixed $parent
		 */
		public function __construct(){
			// call the parent constructor
			parent::__construct();
			// set the name of the field
			$this->_name = __('oEmbed Resource');
			// permits to make it required
			$this->_required = true;
			// permits the make it show in the table columns
			$this->_showcolumn = true;
			// set as not required by default
			$this->set('required', 'no');
			// set not unique by default
			$this->set('unique', 'no');
			// set to show thumbs in table by default
			$this->set('thumbs', 'yes');

		}

		public function isSortable(){
			return false; // @todo: should we allow to sort by url/driver ?
		}

		public function canFilter(){
			return false; // @todo: should we allow to filter by url/driver ?
		}

		public function canImport(){
			return false;
		}

		public function canPrePopulate(){
			return false;
		}

		public function mustBeUnique(){
			return ($this->get('unique') == 'yes');
		}

		public function allowDatasourceOutputGrouping(){
			return false; // @todo: should we allow to group by url/driver ?
		}

		public function requiresSQLGrouping(){
			return false;
		}

		public function allowDatasourceParamOutput(){
			return false; // @todo: should we allow to output the url ?
		}

		/**
		 * @return array
		 */
		public function getAllowedDrivers() {
			return explode(',', $this->get('driver'));
		}

		public function forceSSL() {
			return ($this->get('force_ssl') == 'yes');
		}


		/* ********** INPUT AND FIELD *********** */


		/**
		 *
		 * Validates input
		 * Called before <code>processRawFieldData</code>
		 * @param $data
		 * @param $message
		 * @param $entry_id
		 */
		public function checkPostFieldData($data, &$message, $entry_id=NULL){

			$message = NULL;
			$required = ($this->get('required') == 'yes');

			if($required && strlen($data) == 0){
				$message = __("'%s' is a required field.", array($this->get('label')));
				return self::__MISSING_FIELDS__;
			}

			$url = $data;
			$driver = ServiceDispatcher::getServiceDriver($url, $this->getAllowedDrivers());

			// valid driver
			if (!$driver && strlen($url) > 0) {
				$message = __("%s: No <code>ServiceDriver</code> found for '%s'.", array($this->get('label'), $url));
				return self::__INVALID_FIELDS__;
			}

			// uniqueness
			if ($this->mustBeUnique() && !$this->checkUniqueness($url, $entry_id)) {
				$message = __("%s: This field must be unique. An entry already contains this url.", array($this->get('label'), $url));
				return self::__INVALID_FIELDS__;
			}

			return self::__OK__;
		}


		/**
		 *
		 * Utility (private) function to check if the $url param
		 * is not already in the DB for this field
		 * @param $url
		 */
		private function checkUniqueness($url, $entry_id = null) {
			$id = $this->get('field_id');

			$query = "
				SELECT count(`id`) as `c` FROM `tbl_entries_data_$id`
				WHERE `url` = '$url'
			";

			if ($entry_id != null) {
				$query .= " AND `entry_id` != $entry_id";
			}

			$count = Symphony::Database()->fetchVar('c', 0, $query);

			return $count == null || $count == 0;
		}

		/**
		 *
		 * Process data before saving into databse.
		 * Also,
		 * Fetches oEmbed data from the source
		 *
		 * @param array $data
		 * @param int $status
		 * @param boolean $simulate
		 * @param int $entry_id
		 *
		 * @return Array - data to be inserted into DB
		 */
		public function processRawFieldData($data, &$status, &$message = null, $simulate = false, $entry_id = null) {
			$status = self::__OK__;

			$errorFlag = false;

			$xml = array();

			// capture the url in the field's data
			$url = trim($data);

			// if no url is given
			if (empty($url)) {
				// If this is a required field, flag the missing fields status.
				if ($this->get('required') == 'yes') {
					$errorFlag = true;
					$status = self::__MISSING_FIELDS__;

					// stop the insert
					return false;
				} else {

					// let the value be empty
					return true;
				}
			}

			// store a pointer to the driver
			$driver = ServiceDispatcher::getServiceDriver($url, $this->getAllowedDrivers());


			// check if we have a driver first and that this driver is allowed
			if(!$driver) {
				$status =  self::__INVALID_FIELDS__;
				$errorFlag = true;
				return array( // keep only the url, so the user do not have to type it back
					'url' => $url
				);

			} else {
				// get xml data
				$params = array(
					'url' => $url,
					'query_params' => $this->get('query_params')
				);
				$xml = $driver->getDataFromSource($params, $errorFlag);

				// HACK: couldn't figure out how to validate in checkPostFieldData() and then prevent
				// this processRawFieldData function executing, since it requires valid data to load the XML
				// thanks @nickdunn
				// NOTE: The $message stuff won't do anything due to a Symphony bug
				// https://github.com/symphonycms/symphony-2/issues/879

				// if $xml is NOT an array OR if $errorFlag and no error message...
				if (!is_array($xml) || ($errorFlag && !isset($xml['error']))) {
					//$errorFlag = true;
					$message = __('Failed to load oEmbed data');
					$status =  self::__INVALID_FIELDS__;

					// set the array, as we still want to save the url
					if (!is_array($xml)) {
						$xml = array();
					}
				}
				// else, if we can find a 'error' value
				else if (isset($xml['error'])) {
					$errorFlag = true;
					$message = __('Exception occurred: %s', array( $xml['error'] ));
					$status =  self::__INVALID_FIELDS__;
				}
			}

			$row = array(
				'url' => $url,
				'res_id' => $xml['id'],
				'url_oembed_xml' => $xml['url'],
				'oembed_xml' => $xml['xml'],
				'title' => $xml['title'],
				'thumbnail_url' => $xml['thumb'],
				'driver' => $xml['driver']
			);

			// SSL
			if ($this->forceSSL()) {
				$driver->convertToSSL($row);
			}

			// return row
			return $row;
		}

		/**
		 * This function permits parsing different field settings values
		 *
		 * @param array $settings
		 *	the data array to initialize if necessary.
		 */
		public function setFromPOST(Array $settings = array()) {

			// call the default behavior
			parent::setFromPOST($settings);

			// declare a new setting array
			$new_settings = array();

			// set new settings
			$new_settings['unique'] = 		( isset($settings['unique']) 		&& $settings['unique'] == 'on' ? 'yes' : 'no');
			$new_settings['thumbs'] = 		( isset($settings['thumbs']) 		&& $settings['thumbs'] == 'on' ? 'yes' : 'no');
			$new_settings['driver'] = 		( isset($settings['driver']) 		&& is_array($settings['driver']) ? implode(',', $settings['driver']) : null);
			$new_settings['query_params'] = ( isset($settings['query_params'])  && !!$settings['query_params'] ? $settings['query_params'] : null);
			$new_settings['force_ssl'] = 	( isset($settings['force_ssl']) 	&& $settings['force_ssl'] == 'on' ? 'yes' : 'no');

			// save it into the array
			$this->setArray($new_settings);
		}


		/**
		 *
		 * Validates the field settings before saving it into the field's table
		 */
		public function checkFields(Array &$errors, $checkForDuplicates) {
			parent::checkFields($errors, $checkForDuplicates);

			$driver = $this->get('driver');

			if (empty($driver)) {
				$errors['driver'] = __('You must select at least one service in order to use the oEmbed field.');
			}

			return (!empty($errors) ? self::__ERROR__ : self::__OK__);
		}

		/**
		 *
		 * Save field settings into the field's table
		 */
		public function commit() {

			// if the default implementation works...
			if(!parent::commit()) return false;

			//var_dump($this->get());die;

			$id = $this->get('id');
			$refresh = $this->get('refresh');
			$unique = $this->get('unique');
			$thumbs = $this->get('thumbs');
			$drivers = $this->get('driver');
			$query_params = $this->get('query_params');
			$force_ssl = $this->get('force_ssl');

			// exit if there is no id
			if($id == false) return false;

			// declare an array contains the field's settings
			$settings = array();

			// the field id
			$settings['field_id'] = $id;

			// the 'unique' setting
			$settings['unique'] =  empty($unique) ? 'no' : $unique;

			// the 'thumbs' setting
			$settings['thumbs'] = empty($thumbs) ? 'no' : $thumbs;

			// @todo implement this
			// do not comment the next line, as we can not store NULL into it
			$settings['refresh'] = $refresh;

			// Permit only some specific drivers
			$settings['driver'] = empty($drivers) || count($drivers) < 0 ? null : $drivers;

			// Force SSL setting
			$settings['force_ssl'] = empty($force_ssl) ? 'no' : $force_ssl;

			// Extra request parameters (@see issue #11)
			if (!!$query_params && $query_params{0} != '&') {
				$query_params = '&' . $query_params;
			}
			$settings['query_params'] = empty($query_params) ? null : $query_params;

			$tbl = self::FIELD_TBL_NAME;

			Symphony::Database()->query("DELETE FROM `$tbl` WHERE `field_id` = '$id' LIMIT 1");

			// return if the SQL command was successful
			return Symphony::Database()->insert($settings, $tbl);

		}

		/**
		 *
		 * Remove the entry data of this field from the database, when deleting an entry
		 * @param integer|array $entry_id
		 * @param array $data
		 * @return boolean
		 */
		public function entryDataCleanup($entry_id, array $data) {
			if (empty($entry_id) || !parent::entryDataCleanup($entry_id, $data)) {
				return false;
			}

			return true;
		}

		/**
		 *
		 * This function allows Fields to cleanup any additional things before it is removed
		 * from the section.
		 * @return boolean
		 */
		public function tearDown() {
			return parent::tearDown();
		}




		/* ******* DATA SOURCE ******* */

		/**
		 *
		 * This array will populate the Datasource included elements.
		 * @return array - the included elements
		 * @see http://symphony-cms.com/learn/api/2.2.3/toolkit/field/#fetchIncludableElements
		 */
		public function fetchIncludableElements() {
			$elements = parent::fetchIncludableElements();

			return $elements;
		}

		/**
		 * Appends data into the XML tree of a Data Source
		 * @param $wrapper
		 * @param $data
		 */
		public function appendFormattedElement(&$wrapper, $data) {

			if(!is_array($data) || empty($data)) return;

			// If cache has expired refresh the data array from parsing the API XML
			/*if ((time() - $data['last_updated']) > ($this->_fields['refresh'] * 60)) {
				$data = VimeoHelper::updateClipInfo($data['clip_id'], $this->_fields['id'], $wrapper->getAttribute('id'), $this->Database);
			}*/

			// root for all values
			$field = new XMLElement($this->get('element_name'));

			$field->setAttributeArray(array(
				'id' => $data['res_id']
			));

			$title = new XMLElement('title', General::sanitize($data['title']));
			$title->setAttribute('handle', Lang::createHandle($data['title']));
			$field->appendChild($title);
			$field->appendChild(new XMLElement('url', General::sanitize($data['url'])));
			$field->appendChild(new XMLElement('thumbnail', General::sanitize($data['thumbnail_url'])));
			$field->appendChild(new XMLElement('driver', General::sanitize($data['driver'])));

			$protocols = new XMLElement('protocols');
			if ($this->forceSSL()) {
				$protocols->appendChild(new XMLElement('item', 'https'));
			}
			$protocols->appendChild(new XMLElement('item', 'http'));
			$field->appendChild($protocols);

			$xml = new DomDocument();

			// if we can successfully load the XML data into the
			// DOM object while ignoring errors (@)
			if (@$xml->loadXML($data['oembed_xml'])) {

				$xml->preserveWhiteSpace = true;
				$xml->formatOutput = true;
				$xml->normalize();

				// store a pointer to the driver
				// @todo: use the `driver` column
				$driver = ServiceDispatcher::getServiceDriver($data['url']);

				// get the root node
				$xml_root = $xml->getElementsByTagName($driver->getRootTagName())->item(0);

				// not needed anymore
				// if we did not found anything, try to look for a 'error' tag
				/*if (empty($xml_root)) {
					$xml_root = $xml->getElementsByTagName('error')->item(0);
				}*/

				// if we've found a root node
				if (!empty($xml_root)) {
					// save it as a string
					$xml = $xml->saveXML($xml_root);
					// set it as the 'value' of the field
					// BEWARE: it will be just a string, since the
					// value we set is xml. It's just a hack to pass
					// the value from the DOMDocument object to the XMLElement
					$field->setValue($xml, false);
				}

			} else {
				// loading the xml string into the DOMDocument did not work
				// so we will add a errors message into the result
				$error = new XMLElement();

				$error->setValue(__('Error while loading the xml into the document'));

				$field->appendChild($error);
			}

			$wrapper->appendChild($field);
		}




		/* ********* UI *********** */

		/**
		 *
		 * Builds the UI for the publish page
		 * @param XMLElement $wrapper
		 * @param mixed $data
		 * @param mixed $flagWithError
		 * @param string $fieldnamePrefix
		 * @param string $fieldnamePostfix
		 */
		public function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL) {

			$isRequired = $this->get('required') == 'yes';
			$isUnique = $this->get('unique') == 'yes';

			$value = General::sanitize($data['url']);
			$label = Widget::Label($this->get('label'));

			// not required and unique label
			if(!$isRequired && $isUnique) {
				$label->appendChild(new XMLElement('i', __('Optional') . ', ' . __('Unique')));

			// not required label
			} else if(!$isRequired) {
				$label->appendChild(new XMLElement('i', __('Optional')));

			// unique label
			} else if($isUnique) {
				$label->appendChild(new XMLElement('i', __('Unique')));
			}

			// input form
			$url = new XMLElement('input');
			$url->setAttribute('type', 'text');
			$url->setAttribute('name', 'fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix);
			$url->setAttribute('value', $value);

			$drivers = new XMLElement('div',
				__('Supported services: <i>%s</i>',
					array($this->get('driver'))
				)
			);

			if (strlen($value) == 0 || $flagWithError != NULL) {

				// do nothing

			} else {

				// hides input and drivers
				$url->setAttribute('class', 'irrelevant');
				$drivers->setAttribute('class', 'irrelevant');

				// create a resource container
				$res_container = new XMLElement('span');
				$res_container->setAttribute('class', 'frame');

				$change = new XMLElement('a', __('Change'));
				$change->setAttribute('class', 'change');

				$or = new XMLElement('span', __(' or '));

				$remove = new XMLElement('a', __('Remove'));
				$remove->setAttribute('class', 'change remove');

				$e_options = array(
					'location' => $this->get('location'),
					'width' => '640',
					'height' => '360',
					'width_side' => '320',
					'height_side' => '160'
				);

				// get the embed code
				$driver = ServiceDispatcher::getServiceDriver($value);
				$embed = null;
				if (!$driver) {
					$embed = __('Error. Service unknown.');
				} else {
					$embed = $driver->getEmbedCode($data, $e_options);
				}

				$res_container->setValue("<div>$embed</div>");

				$res_container->appendChild($change);
				$res_container->appendChild($or);
				$res_container->appendChild($remove);

				$label->appendChild($res_container);
			}

			// append the input tag into the label
			$label->appendChild($url);

			// append the allowed drivers list
			$label->appendChild($drivers);

			// error management
			if($flagWithError != NULL) {
				$wrapper->appendChild(Widget::wrapFormElementWithError($label, $flagWithError));
			} else {
				$wrapper->appendChild($label);
			}
		}

		/**
		 *
		 * Builds the UI for the field's settings when creating/editing a section
		 * @param XMLElement $wrapper
		 * @param array $errors
		 */
		public function displaySettingsPanel(&$wrapper, $errors=NULL){

			/* first line, label and such */
			parent::displaySettingsPanel($wrapper, $errors);

			/* new line, drivers */
			$driv_wrap = new XMLElement('div', NULL, array('class'=>'oembed-drivers'));
			$driv_title = new XMLElement('label',__('Supported services <i>Select to enable the service in the publish page</i>'));
			$driv_title->appendChild($this->generateDriversSelect());
			if (isset($errors['driver'])) {
				$driv_title = Widget::wrapFormElementWithError($driv_title, $errors['driver']);
			}
			$driv_wrap->appendChild($driv_title);

			/* new line, update settings */
			$set_wrap = new XMLElement('div', NULL, array('class'=>'group'));
			$label = Widget::Label(__('Update cache <em>in minutes</em> (leave blank to never update) <i>Optional</i>'));
			$label->appendChild(Widget::Input('fields['.$this->get('sortorder').'][refresh]', $this->get('refresh')));
			$set_wrap->appendChild($label);

			/* new line, request params */
			// Fixes issue #11
			$par_wrap = new XMLElement('div', NULL, array('class'=>'oembed-params-settings'));
			$par_title = new XMLElement('label', __('Request URL Parameters (Appended to the query string) <i>Optional</i>'));
			$par_title->appendChild(Widget::Input('fields['.$this->get('sortorder').'][query_params]', $this->get('query_params')));
			$par_wrap->appendChild($par_title);

			/* new line, check boxes */
			$chk_wrap = new XMLElement('div', NULL, array('class' => 'compact'));
			$chk_wrap->appendChild(new XMLElement('label', __('Other properties'), array('class'=>'oembed-other-title') ));
			$this->appendRequiredCheckbox($chk_wrap);
			$this->appendShowColumnCheckbox($chk_wrap);
			$this->appendMustBeUniqueCheckbox($chk_wrap);
			$this->appendShowThumbnailCheckbox($chk_wrap);
			$this->appendForceSSLCheckbox($chk_wrap);

			/* append to wrapper */
			$wrapper->appendChild($driv_wrap);
			$wrapper->appendChild($par_wrap);
			$wrapper->appendChild($chk_wrap);

		}

		private function generateDriversSelect() {
			$drivers = ServiceDispatcher::getAllDriversNames();

			sort($drivers, SORT_STRING);
			$drivers_options = array();
			foreach ($drivers as $driver) {
				$selected = strpos($this->get('driver'), $driver) > -1;
				$drivers_options[] = array($driver, $selected);
			}

			return Widget::Select('fields['.$this->get('sortorder').'][driver][]', $drivers_options, array('multiple'=>'multiple'));
		}

		/**
		 *
		 * Utility (private) function to append a checkbox for the 'unique' setting
		 * @param XMLElement $wrapper
		 */
		private function appendMustBeUniqueCheckbox(&$wrapper) {
			$label = new XMLElement('label');
			$chk = new XMLElement('input', NULL, array('name' => 'fields['.$this->get('sortorder').'][unique]', 'type' => 'checkbox'));

			$label->appendChild($chk);
			$label->setValue(__('Make this field unique'), false);

			if ($this->get('unique') == 'yes') {
				$chk->setAttribute('checked','checked');
			}

			$wrapper->appendChild($label);
		}

		/**
		 *
		 * Utility (private) function to append a checkbox for the 'thumbs' setting
		 * @param XMLElement $wrapper
		 */
		private function appendShowThumbnailCheckbox(&$wrapper) {
			$label = new XMLElement('label');
			$chk = new XMLElement('input', NULL, array('name' => 'fields['.$this->get('sortorder').'][thumbs]', 'type' => 'checkbox'));

			$label->appendChild($chk);
			$label->setValue(__('Show thumbnails in table'), false);

			if ($this->get('thumbs') == 'yes') {
				$chk->setAttribute('checked','checked');
			}

			$wrapper->appendChild($label);
		}

		/**
		 *
		 * Utility (private) function to append a checkbox for the 'force_ssl' setting
		 * @param XMLElement $wrapper
		 */
		private function appendForceSSLCheckbox(&$wrapper) {
			$label = new XMLElement('label');
			$chk = new XMLElement('input', NULL, array('name' => 'fields['.$this->get('sortorder').'][force_ssl]', 'type' => 'checkbox'));

			$label->appendChild($chk);
			$label->setValue(__('Force protocol-less embeding (allow ssl, only if the drivers supports it)'), false);

			if ($this->forceSSL()) {
				$chk->setAttribute('checked','checked');
			}

			$wrapper->appendChild($label);
		}

		/**
		 *
		 * Build the UI for the table view
		 * @param Array $data
		 * @param XMLElement $link
		 * @return string - the html of the link
		 */
		public function prepareTableValue($data, XMLElement $link=NULL){

			$url = $data['url'];
			$thumb = $data['thumbnail_url'];
			$textValue = $this->preparePlainTextValue($data, $data['res_id']);
			$value = NULL;

			// no url = early exit
			if(strlen($url) == 0) return NULL;

			// no thumbnail or the parameter is not set ?
			if (empty($thumb) || $this->get('thumbs') != 'yes') {
				// if not use the title or the url as value
				$value = $textValue;
			} else {
				// create a image
				$img_path = URL . '/image/1/0/40/1/' .  str_replace('http://', '',$thumb);

				$value = '<img src="' . $img_path .'" alt="' . General::sanitize($data['title']) .'" height="40" />';
			}

			// does this cell serve as a link ?
			if (!!$link){
				// if so, set our html as the link's value
				$link->setValue($value);
				$link->setAttribute('title', $textValue . ' | ' . $link->getAttribute('title'));

			} else {
				// if not, wrap our html with a external link to the resource url
				$link = new XMLElement('a',
					$value,
					array('href' => $url, 'target' => '_blank', 'title' => $textValue)
				);
			}

			// returns the link's html code
			return $link->generate();
		}

		/**
		 *
		 * Return a plain text representation of the field's data
		 * @param array $data
		 * @param int $entry_id
		 */
		public function preparePlainTextValue($data, $entry_id = null) {
			return (
				isset($data['title'])
					? General::sanitize($data['title'])
					: (isset($data['url']) ? $data['url'] : $entry_id)
			);
		}





		/* ********* SQL Data Definition ************* */

		/**
		 *
		 * Creates table needed for entries of invidual fields
		 */
		public function createTable(){
			$id = $this->get('id');

			return Symphony::Database()->query("
				CREATE TABLE `tbl_entries_data_$id` (
					`id` int(11) 		unsigned NOT NULL auto_increment,
					`entry_id` 			int(11) unsigned NOT NULL,
					`res_id` 			varchar(128),
					`url` 				varchar(2048),
					`url_oembed_xml` 	varchar(2048),
					`title` 			varchar(2048),
					`thumbnail_url` 	varchar(2048),
					`oembed_xml` 		text,
					`dateCreated` 		timestamp DEFAULT CURRENT_TIMESTAMP,
					`driver`			varchar(50),
					PRIMARY KEY  (`id`),
					KEY `entry_id` (`entry_id`)
				)  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public static function createFieldTable() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				CREATE TABLE IF NOT EXISTS `$tbl` (
					`id` 			int(11) unsigned NOT NULL auto_increment,
					`field_id` 		int(11) unsigned NOT NULL,
					`refresh` 		int(11) unsigned NULL,
					`driver` 		varchar(250) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				)  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}

		/**
		 * Updates the table for the new settings: `unique`
		 */
		public static function updateFieldTable_Unique() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				ALTER TABLE  `$tbl`
					ADD COLUMN `unique` enum('yes','no') NOT NULL DEFAULT 'no'
			");
		}

		/**
		 * Updates the table for the new settings: `thumbs`
		 */
		public static function updateFieldTable_Thumbs() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				ALTER TABLE  `$tbl`
					ADD COLUMN `thumbs` enum('yes','no') NOT NULL DEFAULT 'no'
			");
		}

		/**
		 * Updates the table for the new settings: `params_set_id`
		 */
		public static function updateFieldTable_QueryParams() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				ALTER TABLE  `$tbl`
					ADD COLUMN `query_params` varchar(1024) NULL
			");
		}

		public static function updateFieldTable_Driver() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				ALTER TABLE  `$tbl`
					MODIFY COLUMN `driver` varchar(250) NOT NULL
			");
		}

		public static function updateFieldData_Driver() {

			$tbl = self::FIELD_TBL_NAME;

			// allow all drivers for fields that already exists
			$drivers = MySQL::cleanValue( implode(',',ServiceDispatcher::getAllDriversNames()) );

			return Symphony::Database()->query("
				UPDATE `$tbl`
					SET `driver` = '$drivers'
			");
		}

		public static function updateDataTable_Driver() {

			$fm = new FieldManager(Symphony::Engine());

			// get all entries tables of type oEmbed
			$fields = $fm->fetch(null, null, 'ASC', 'id', 'oembed');

			// make sure the new driver column is add to
			// fields that already exists
			foreach ($fields as $field) {

				$id = $field->get('id');

				// test is the colum exist
				$col = Symphony::Database()->fetch("
					SHOW COLUMNS FROM `tbl_entries_data_$id`
						WHERE `field` = 'driver'
				");

				// if the col doest not exists
				if (!is_array($col) || count($col) == 0) {

					$ret = Symphony::Database()->query("
						ALTER TABLE  `tbl_entries_data_$id`
							ADD COLUMN `driver`	varchar(50) NOT NULL
					");

					if (!$ret) {
						return false;
					}
				}
			}
			return true;
		}

		public static function updateFieldTable_ForceSSL() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
					ALTER TABLE  `$tbl`
					ADD COLUMN `force_ssl` ENUM('yes','no') NOT NULL DEFAULT 'no'
					");
		}

		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public static function deleteFieldTable() {
			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				DROP TABLE IF EXISTS `$tbl`
			");
		}

	}