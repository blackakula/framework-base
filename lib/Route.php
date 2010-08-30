<?php
  class Route {
    const REGEXP_CHAR = '@';
    private $_regexp;
    private $_vars;
    private $_params;

    public function __construct($regexp) { $this->_regexp = Route::regexp_str($regexp); }

    private static function regexp_str($str) { return Route::REGEXP_CHAR.str_replace(Route::REGEXP_CHAR,'\\'.Route::REGEXP_CHAR,$str).Route::REGEXP_CHAR; }

    public function set_params($vars,$params) {
      $this->_vars = array();
      $this->_params = array();
      foreach ($vars as $k => $v)
        $this->_vars[$k] = array_key_exists($v,$params) ? array($v,Route::regexp_str($params[$v])) : $v;
      foreach ($params as $k => $v)
        if (preg_match('/^\\:[a-z_]+$/',$k) === 0)
          $this->_params[$k] = $v;
    }
  }
?>
