<?php
  class HttpHeader {

    protected $_headers;
    protected $_redirect;
    protected $_status;
    protected $_content_type;

    public function __construct() {
      $this->_headers = array();
      $this->_redirect = false;
      $this->_content_type = 'text/html';
      $this->_status = 200;
    }

    public function redirect($location, $code = 302) {
      $this->_redirect = array($location, $code);
    }

    public function has_redirect() {
      return ($this->_redirect) ? true : false;
    }

    public function try_redirect() {
      if ($this->_redirect) {
        header('Location: '.$this->_redirect[0],true,$this->redirect[1]);
        die();
      }
    }

    public function format($format) {
      $this->_content_type = $format;
    }

    public function header($text,$replace = false) {
      array_push($this->_headers,array($text,$replace));
    }

    public function page404() {
      $this->_status = 404;
    }

    public function is404() {
      return ($this->_status == 404);
    }
  }
?>
