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
        $this->_vars[$k] = array_key_exists($v,$params) ? array($v,Route::regexp_str('^'.$params[$v].'$')) : $v;
      foreach ($params as $k => $v)
        if (preg_match('/^\\:[a-z_]+$/',$k) === 0)
          $this->_params[$k] = (string)$v;
    }

    public function check($url) {
      if (preg_match($this->_regexp,$url,$matches) === 0) return false;
      $result_params = array();
      foreach ($this->_vars as $k => $v) {
        if (is_array($v)) {
          if (preg_match($v[1],$matches[$k+1]) === 0) return false;
          else $v = $v[0];
        }
        $key = substr($v,1);
        if (!empty($matches[$k+1]) || !array_key_exists($key,$this->_params))
          $result_params[$key] = $matches[$k+1];
      }
      return array_merge($this->_params,$result_params);
    }
  }
?>
