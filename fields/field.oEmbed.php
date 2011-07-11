<?php
	
	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');
	
	require_once(EXTENSIONS . '/vimeo_videos/lib/vimeo_helper.php');
	
	/**
	 * 
	 * Field class that will represent an oEmbed ressource
	 * @author Nicolas
	 * 
	 * Based on @nickdunn's Vimeo field: https://github.com/nickdunn/vimeo_videos/blob/master/fields/field.vimeo_video.php
	 *
	 */
	class FieldOembed extends Field {
		public function __construct(&$parent){
			parent::__construct($parent);
			$this->_name = __('oEmbed Ressource Field');
			$this->_required = false;
			$this->set('required', 'no');
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
			
			/*$value = General::sanitize($data['clip_id']);
			$label = Widget::Label($this->get('label'));
			
			$clip_id = new XMLElement('input');
			$clip_id->setAttribute('type', 'text');
			$clip_id->setAttribute('name', 'fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix);
			$clip_id->setAttribute('value', $value);
			
			if (strlen($value) == 0 || $flagWithError != NULL) {
				
				if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));
				
			} else {
				
				$clip_id->setAttribute('class', 'hidden');
				
				$video_container = new XMLElement('span');
				$video_container->setAttribute('class', 'frame');
				
				$clip_url = 'http://www.vimeo.com/moogaloop.swf?clip_id=' . $value . '&amp;server=www.vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1';
				
				$video = new XMLElement('object');
				$video->setAttribute('width', $data['width']);
				$video->setAttribute('height', $data['height']);
				
				$param = new XMLElement('param');
				$param->setAttribute('allowfullscreen', 'true');
				$video->appendChild($param);
				
				$param = new XMLElement('param');
				$param->setAttribute('allowscriptaccess', 'always');
				$video->appendChild($param);
				
				$param = new XMLElement('param');
				$param->setAttribute('movie', $clip_url);
				$video->appendChild($param);
				
				$embed = new XMLElement('embed');
				$embed->setAttribute('src', $clip_url);
				$embed->setAttribute('allowfullscreen', 'true');
				$embed->setAttribute('allowscriptaccess', 'always');
				$embed->setAttribute('width', $data['width']);
				$embed->setAttribute('height', $data['height']);
				$embed->setAttribute('type', 'application/x-shockwave-flash');
				
				$video->appendChild($embed);
				
				$meta = new XMLElement('span', $data['title'] . ' by <a href="' . $data["user_url"] . '">' . $data['user_name'] . '</a>');
				$meta->setAttribute('class', 'meta');
				$video->appendChild($meta);
				
				$meta = new XMLElement('span', $data['plays'] . ' plays');
				$meta->setAttribute('class', 'meta');
				$video->appendChild($meta);
				
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
		
		function displayDatasourceFilterPanel(&$wrapper, $data=NULL, $errors=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){
			/*$wrapper->appendChild(new XMLElement('h4', $this->get('label') . ' <i>'.$this->Name().'</i>'));
			$label = Widget::Label('Clip ID');
			$label->appendChild(Widget::Input('fields[filter]'.($fieldnamePrefix ? '['.$fieldnamePrefix.']' : '').'['.$this->get('id').']'.($fieldnamePostfix ? '['.$fieldnamePostfix.']' : ''), ($data ? General::sanitize($data) : NULL)));
			$wrapper->appendChild($label);*/

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
			/*return (
				isset($data['title'])
					? $data['title']
					: null
			);*/
		}
		
		function createTable(){
			/*return $this->_engine->Database->query(
				"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`entry_id` int(11) unsigned NOT NULL,
				`clip_id` int(11) unsigned NOT NULL,
				`title` varchar(255) default NULL,
				`caption` text,
				`thumbnail_url` varchar(255) default NULL,
				`thumbnail_width` int(11) unsigned NOT NULL,
				`thumbnail_height` int(11) unsigned NOT NULL,
				`thumbnail_medium_url` varchar(255) default NULL,
				`thumbnail_medium_width` int(11) unsigned NOT NULL,
				`thumbnail_medium_height` int(11) unsigned NOT NULL,
				`thumbnail_small_url` varchar(255) default NULL,
				`thumbnail_small_width` int(11) unsigned NOT NULL,
				`thumbnail_small_height` int(11) unsigned NOT NULL,
				`width` int(11) unsigned NOT NULL,
				`height` int(11) unsigned NOT NULL,
				`duration` int(11) unsigned NOT NULL,
				`plays` int(11) unsigned NOT NULL,
				`user_name` varchar(255) default NULL,
				`user_url` varchar(255) default NULL,
				`last_updated` int(11) unsigned NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `entry_id` (`entry_id`)
				);"
			);*/
		}
		
		public function displaySettingsPanel(&$wrapper, $errors=NULL){
			parent::displaySettingsPanel($wrapper, $errors);
			/*$this->appendRequiredCheckbox($wrapper);
			$this->appendShowColumnCheckbox($wrapper);
			
			$label = Widget::Label('Update cache (minutes; leave blank to never update) <i>Optional</i>');
			$label->appendChild(Widget::Input('fields['.$this->get('sortorder').'][refresh]', $this->get('refresh')));
			$wrapper->appendChild($label);
			*/
		}
		
		/*function buildSortingSQL(&$joins, &$where, &$sort, $order='ASC'){
			$joins .= "INNER JOIN `tbl_entries_data_".$this->get('id')."` AS `ed` ON (`e`.`id` = `ed`.`entry_id`) ";
			$sort = 'ORDER BY ' . (strtolower($order) == 'random' ? 'RAND()' : "`ed`.`plays` $order");
		}*/
		
		/*public function buildDSRetrievalSQL($data, &$joins, &$where, $andOperation = false) {
			
			$field_id = $this->get('id');
			
			if (self::isFilterRegex($data[0])) {
				$this->_key++;
				$pattern = str_replace('regexp:', '', $this->cleanValue($data[0]));
				$joins .= "
					LEFT JOIN
						`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
						ON (e.id = t{$field_id}_{$this->_key}.entry_id)
				";
				$where .= "
					AND t{$field_id}_{$this->_key}.clip_id REGEXP '{$pattern}'
				";
				
			} elseif ($andOperation) {
				foreach ($data as $value) {
					$this->_key++;
					$value = $this->cleanValue($value);
					$joins .= "
						LEFT JOIN
							`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
							ON (e.id = t{$field_id}_{$this->_key}.entry_id)
					";
					$where .= "
						AND t{$field_id}_{$this->_key}.clip_id = '{$value}'
					";
				}
				
			} else {
				if (!is_array($data)) $data = array($data);
				
				foreach ($data as &$value) {
					$value = $this->cleanValue($value);
				}
				
				$this->_key++;
				$data = implode("', '", $data);
				$joins .= "
					LEFT JOIN
						`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
						ON (e.id = t{$field_id}_{$this->_key}.entry_id)
				";
				$where .= "
					AND t{$field_id}_{$this->_key}.clip_id IN ('{$data}')
				";
			}
			
			return true;
			
		}*/
		
	}