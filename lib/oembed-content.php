<?php

	/**
	 * @package oembed_field
	 */
	class OembedContentType implements ContentType {
		public function getName() {
			return __('oEmbed');
		}

		public function appendSettingsHeaders(HTMLPage $page) {
			$url = URL . '/extensions/content_field/assets';
			$page->addStylesheetToHead($url . '/settings.css', 'screen');
		}

		public function appendSettingsInterface(XMLElement $wrapper, $field_name, StdClass $settings = null, MessageStack $errors) {

			// Select available drivers:
			$driv_wrap = new XMLElement('div');
			$driv_wrap->addClass('oembed-drivers');

			$driv_title = new XMLElement('label');
			$driv_title->setValue(__('Supported services <i>Select to enable the service in the publish page</i>'));
			$driv_title->appendChild(Widget::Select(
				"{$field_name}[drivers][]",
				$this->getDriversSelectOptions($settings),
				array('multiple'=>'multiple')
			));

			if (isset($errors->{'drivers'})) {
				$driv_title = Widget::wrapFormElementWithError(
					$driv_title, $errors->{'drivers'}
				);
			}

			$driv_wrap->appendChild($driv_title);

			// Fixes issue #11 (Got a better description?)
			$par_wrap = new XMLElement('div');
			$par_wrap->addClass('oembed-params-settings');

			$par_title = new XMLElement('label');
			$par_title->setValue(__('Request URL Parameters (Appended to the query string) <i>Optional</i>'));
			$par_title->appendChild(Widget::Input(
				"{$field_name}[parameters]",
				$settings->{'parameters'}
			));

			$par_wrap->appendChild($par_title);

			$wrapper->appendChild($driv_wrap);
			$wrapper->appendChild($par_wrap);
		}

		/**
		 * @todo Put this somewhere it doesn't need to be declared twice.
		 */
		private function getDriversSelectOptions(StdClass $settings) {
			$drivers = ServiceDispatcher::getAllDriversNames();
			sort($drivers, SORT_STRING);
			$drivers_options = array();

			foreach ($drivers as $driver) {
				$selected = in_array($driver, $settings->{'drivers'});
				$drivers_options[] = array($driver, $selected);
			}

			return $drivers_options;
		}

		public function sanitizeSettings($settings) {
			if (is_array($settings)) {
				$settings = (object)$settings;
			}

			else if (is_object($settings) === false) {
				$settings = new StdClass();
			}

			if (isset($settings->{'drivers'}) === false) {
				$settings->{'drivers'} = array();
			}

			if (isset($settings->{'parameters'}) === false) {
				$settings->{'parameters'} = '';
			}

			return $settings;
		}

		public function validateSettings(StdClass $settings, MessageStack $errors) {
			if (empty($settings->{'drivers'})) {
				$errors->{'drivers'} = __('You must select at least one service in order to use the oEmbed field.');

				return false;
			}

			return true;
		}

		public function appendPublishHeaders(HTMLPage $page) {

		}

		public function appendPublishInterface(XMLElement $wrapper, $field_name, StdClass $settings, StdClass $data, MessageStack $errors, $entry_id = null) {
			$url = new XMLElement('input');
			$url->setAttribute('type', 'text');
			$url->setAttribute('name', "{$field_name}[data][value]");
			$url->setAttribute('value', $data->{'value'});

			$drivers = new XMLElement('div', __(
				'Supported services: <i>%s</i>', array(
					implode(', ', $settings->{'drivers'})
				)
			));

			if (isset($errors->{'value'})) {
				$url = Widget::wrapFormElementWithError(
					$url, $errors->{'value'}
				);
			}

			if (strlen($data->{'value'})) {
				// Hides input and drivers:
				$url->setAttribute('class', 'irrelevant');
				$drivers->setAttribute('class', 'irrelevant');

				// Create a resource container:
				$res_container = new XMLElement('span');
				$res_container->setAttribute('class', 'frame');

				$change = new XMLElement('a', __('Change'));
				$change->setAttribute('class', 'change');

				$or = new XMLElement('span', __(' or '));

				$remove = new XMLElement('a', __('Remove'));
				$remove->setAttribute('class', 'change remove');

				// Get the embed code:
				$driver = ServiceDispatcher::getServiceDriver($data->{'value'});
				$embed = __('Error. Service unknown.');

				if ($driver instanceof ServiceDriver) {
					$embed = $driver->getEmbedCode(
						array(
							'oembed_xml'	=> $data->{'xml'}
						),
						array(
							'location'		=> 'sidebar',
							'width_side'	=> '320',
							'height_side'	=> '180'
						)
					);
				}

				$res_container->setValue("<div>{$embed}</div>");

				$res_container->appendChild($change);
				$res_container->appendChild($or);
				$res_container->appendChild($remove);

				$wrapper->appendChild($res_container);
			}

			// Append the input tag into the label:
			$wrapper->appendChild($url);

			// Append the allowed drivers list:
			$wrapper->appendChild($drivers);
		}

		public function processData(StdClass $settings, StdClass $data, $entry_id = null) {
			// Load the driver and fetch the data:
			$driver = ServiceDispatcher::getServiceDriver($data->{'value'});

			// Couldn't load the driver, return as we where.
			if (($driver instanceof ServiceDriver) === false) return $data;

			$params = array(
				'url'			=> $data->{'value'},
				'query_params'	=> $settings->{'parameters'}
			);
			$values = $driver->getDataFromSource($params, $driver_data);

			return $this->sanitizeData($settings, array(
				'value'		=> $data->{'value'},
				'id'		=> $values['id'],
				'title'		=> $values['title'],
				'driver'	=> $values['driver'],
				'url'		=> $values['url'],
				'thumb'		=> $values['thumb'],
				'xml'		=> $values['xml']
			));
		}

		public function sanitizeData(StdClass $settings, $data) {
			$accept = array('value', 'id', 'title', 'driver', 'url', 'thumb', 'xml');
			$result = (object)array(
				'handle'			=> null,
				'value'				=> null,
				'value_formatted'	=> null,
				'id'				=> null,
				'title'				=> null,
				'driver'			=> null,
				'url'				=> null,
				'thumb'				=> null,
				'xml'				=> null
			);

			if (is_object($data) || is_array($data)) {
				foreach ($data as $key => $value) {
					if (in_array($key, $accept) === false) continue;

					$result->{$key} = $value;
				}
			}

			if (is_string($data) && strlen(trim($data))) {
				$result->{'value'} = $data;
			}

			return $result;
		}

		public function validateData(StdClass $settings, StdClass $data, MessageStack $errors, $entry_id = null) {
			if ($data->{'value'} === null || strlen(trim($data->{'value'})) == 0) {
				$errors->{'value'} = __('URL is a required field.');

				return false;
			}

			// Attempt to load the driver:
			$driver = ServiceDispatcher::getServiceDriver(
				$data->{'value'}, $settings->{'drivers'}
			);

			if (($driver instanceof ServiceDriver) === false) {
				$errors->{'value'} = __('No <code>ServiceDriver</code> found.');

				return false;
			}

			// Check that the driver will return data:
			$params = array(
				'url'			=> $data->{'value'},
				'query_params'	=> $settings->{'parameters'}
			);
			$values = $driver->getDataFromSource($params, $driver_error);

			// No data, or error flagged without a value:
			if (
				is_array($values) === false
				|| ($driver_error && isset($values['error']) === false)
			) {
				$errors->{'value'} = __('Failed to load oEmbed data');

				return false;
			}

			// An error message was returned:
			if (isset($values['error'])) {
				$errors->{'value'} = __(
					'Exception occurred: %s', array(
						$values['error']
					)
				);

				return false;
			}

			return true;
		}

		public function appendFormattedElement(XMLElement $wrapper, StdClass $settings, StdClass $data, $entry_id = null) {
			$wrapper->setAttribute('id', $data->{'id'});

			$title = new XMLElement('title');
			$title->setValue(General::sanitize($data->{'title'}));
			$title->setAttribute('handle', Lang::createHandle($data->{'title'}));
			$wrapper->appendChild($title);

			$wrapper->appendChild(new XMLElement(
				'url', General::sanitize($data->{'value'})
			));
			$wrapper->appendChild(new XMLElement(
				'thumbnail', General::sanitize($data->{'thumb'})
			));
			$wrapper->appendChild(new XMLElement(
				'driver', General::sanitize($data->{'driver'})
			));

			// Enable better error handling:
			libxml_use_internal_errors(true);

			$driver = ServiceDispatcher::getServiceDriver($data->{'value'});
			$xml = new DOMDocument();
			$xml->loadXML($data->{'xml'});

			// Ignore any errors:
			libxml_clear_errors();

			// Find the root element:
			$root = $xml->getElementsByTagName($driver->getRootTagName())->item(0);

			// Data was found:
			if ($root instanceof DOMNode) {
				$oembed = new XMLElement('oembed');

				foreach ($root->childNodes as $node) {
					$element = new XMLElement($node->tagName);
					$element->setValue(General::sanitize($node->textContent));
					$oembed->appendChild($element);
				}

				$wrapper->appendChild($oembed);
			}

			// Nothing found:
			else {
				$error = new XMLElement('error');
				$error->setValue(__('Error while loading the xml into the document'));
				$wrapper->appendChild($error);
			}
		}
	}