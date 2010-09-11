<?php
  class HttpHeader {

    const CT = 'text/html';
    protected $_headers;
    protected $_redirect;
    protected $_status;
    protected $_content_type;
    protected $_charset;
    private $_codes = array(
      200 => 'OK',

      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      307 => 'Temporary Redirect',

      404 => 'Not Found'
    );

    public function __construct() {
      $this->_headers = array();
      $this->_redirect = false;
      $this->_content_type = null;
      $this->_charset = null;
      $this->_status = 200;
    }

    public function redirect($location, $code = 302) {
      $this->_status = $code;
      $this->_redirect = $location;
    }

    public function has_redirect() {
      return ($this->_redirect) ? true : false;
    }

    public function try_redirect() {
      if ($this->_redirect) {
        header('Location: '.$this->_redirect);
        die();
      }
    }

    public function &format($format) {
      $this->_content_type = $format;
      return $this;
    }

    public function &charset($charset = 'utf-8') {
      $this->_charset = $charset;
      return $this;
    }

    public function &header($text,$replace = true) {
      array_push($this->_headers,array($text,$replace));
    }

    public function &page404() {
      $this->_status = 404;
      return $this;
    }

    public function is404() {
      return ($this->_status == 404);
    }

    public function send_headers($send200 = false,$send_ct = false) {
      if ($this->_status !== 200 || $send200) {
        $header_status_text = array_key_exists($this->_status,$this->_codes) ? (' '.$this->_codes[$this->_status]) : '';
        header('HTTP/1.1 '.$this->_status.$header_status_text);
      }
      $this->try_redirect();
      if (!is_null($this->_content_type) || !is_null($this->_charset) || $send_ct) {
        $content_type = is_null($this->_content_type) ? HttpHeader::CT : $this->_content_type;
        $charset = is_null($this->_charset) ? '' : ('; charset='.$this->_charset);
        header('Content-Type: '.$content_type.$charset);
      }
      foreach ($this->_headers as $header)
        header($header[0],$header[1]);
    }
  }
?>
