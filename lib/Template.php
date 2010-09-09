<?php
  class Template extends Hash {
    protected $view_path;
    protected $controller;
    protected $action;
    protected $extention;

    public function __construct($controller,$action,$params) {
      parent::__construct();
      $this->_params = $params;
      $this->view_path = get_config('ROOT_DIR').DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
      $this->controller = $controller;
      $this->action = $action;
      $this->extention = 'php';
    }
    public function layout() {
      $filepath = $this->view_path.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR;
      $filename = $filepath.$this->controller.'.php';
      if (!is_readable($filename)) $filename = $filepath.'layout.'.$this->extention;
      (!is_readable($filename)) ? $this->content() : $this->render($filename);
    }
    public function content() {
      $filename = $this->view_path.DIRECTORY_SEPARATOR.$this->controller.DIRECTORY_SEPARATOR.$this->action.'.'.$this->extention;
      $this->check_render($filename);
    }
    public function render($filename) { include($filename); }
    private function check_render($filename) {
      if (!is_readable($filename))
        throw new RoutingException('Template not found');
      $this->render($filename);
    }
  }
?>
