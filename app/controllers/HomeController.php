<?php
  class HomeController extends ApplicationController {
    public function index() {
      $this->set('title','This is Home page example');
      $this->set('text','This is example variable');
      $this->set('text2','This is example of another variable');
    }
  }
?>
