<?php

  if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

  class serviceTwitter extends ServiceDriver {

    public function __construct() {
      parent::__construct('Twitter', 'twitter.com');
    }

    public function about() {
      return array(
        'author'       => array(
          'email'   => 'brian@briandrum.net',
          'name'    => 'Brian Drum',
          'website' => 'http://briandrum.net'
        ),
        'name'         => $this->Name,
        'release-date' => '2012-01-27',
        'version'      => '1.0'
      );
    }

    public function getOEmbedXmlApiUrl($params) {
      $url = rawurlencode($params['url']);
      return 'https://api.twitter.com/1/statuses/oembed.xml?url=' . $url;
    }

    public function getIdTagName() {
      return 'url';
    }
  }
