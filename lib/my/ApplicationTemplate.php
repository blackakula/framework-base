<?php
  class ApplicationTemplate extends Template {
    public function __construct($controller,$action,$params) {
      parent::__construct($controller,$action,$params);
      $this->extentions = array('php','haml');
    }

    public function render($filename, $ext = 'php') {
      if ($ext !== 'haml') return parent::render($filename,$ext);
      $h = new Haml(); //(file_get_contents($filename));
      echo $h->renderFile(null,$filename,array(),true);
//      echo $h->render(file_get_contents($filename));
    }
  }
?>
