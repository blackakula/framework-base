<?php
  class Hash {
    protected $_params;

    public function __construct() { $this->_params = array(); }
    public function set($key, $value) { $this->_params[$key] = $value; }
    public function get($key = null) { return ($key === null) ? $this->_params : $this->_params[$key]; }
    public function del($key) { unset($this->_params[$key]); }
  }
?>
