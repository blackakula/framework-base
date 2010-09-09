<?php
  class Hash extends SingletonObj {
    protected $_params;

    public function __construct() { $this->_params = array(); parent::__construct(); }
    public function set($key, $value) { $this->_params[$key] = $value; }
    public function &get($key = null) { return ($key === null) ? $this->_params : $this->_params[$key]; }
    public function del($key = null) { if ($key === null) $this->_params = array(); else unset($this->_params[$key]); }
  }
?>
