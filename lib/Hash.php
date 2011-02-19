<?php
  class Hash extends SingletonObj {
    protected $_params;

    public function __construct() { $this->_params = array(); parent::__construct(); }
    public function set($key, $value) { $this->_params[$key] = $value; }
    public function &get($key = null) {
      if (is_null($key))
        return $this->_params;
      return $this->_params[$key];
    }
    public function del($key = null) { if ($key === null) $this->_params = array(); else unset($this->_params[$key]); }
    public function push($key, $value) {
      if (array_key_exists($key, $this->_params) && is_array($this->_params[$key]))
              array_push($this->_params[$key], $value);
    }
  }
?>
