<?php
  class Controller extends Hash {
    protected $params;
    public function __construct($params) {
      parent::__construct();
      $this->readonly();
      $this->params = $params;
    }
  }
?>
