<?php
  class Controller extends Hash {
    protected $params;
    protected $_layout;
    protected $_template;

    public function __construct($params) {
      parent::__construct();
      $this->readonly();
      $this->params = $params;
      $this->_layout = null;
      $this->_template = null;
    }

    public function header() { return get_header(); }
    public function layout() { return $this->_layout; }
    public function template() { return $this->_template; }
  }
?>
