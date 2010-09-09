<?php
  class HomeController extends Controller {
    public function index() {
      $this->set('title','This is Home page example');
      $this->set('text','This is example of another variable');
    }
  }
?>
