<?php
  class SingletonObj {
    private $_readonly;
    public function __construct() { $this->_readonly = false; }
    public function is_readonly() { return $this->_readonly; }
    public function readonly() { $this->_readonly = true; }
  }
?>
