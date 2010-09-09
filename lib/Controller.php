<?php
  class Controller extends Hash {
    protected $params;
    public function __construct($params) {
      $this->readonly();
      $this->params = $params;
    }
  }
?>
