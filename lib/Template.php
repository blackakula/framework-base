<?php
  class Template extends Hash {

    const LAYOUT_FOLDER = 'layouts';
    protected $view_path;
    protected $controller;
    protected $action;
    protected $extentions;
    protected $check_template;

    public function __construct($controller,$action,$params) {
      parent::__construct();
      $this->_params = $params;
      $this->view_path = get_config('ROOT_DIR').DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
      $this->controller = $controller;
      $this->action = $action;
      $this->extentions = array('php');
    }

    private function find_view_extention($full_file_path_without_extention) {
      foreach ($this->extentions as $extention){
        $filename = $full_file_path_without_extention.'.'.$extention;
        if (is_readable($filename)) return array($filename,$extention);
      }
      return false;
    }

    public function layout() {
      $filepath = $this->view_path.DIRECTORY_SEPARATOR.(Template::LAYOUT_FOLDER).DIRECTORY_SEPARATOR;
      $layout = $this->find_view_extention($filepath.$this->controller);
      if (!$layout) $layout = $this->find_view_extention($filepath.'layout');
      $layout ? $this->render($layout[0],$layout[1]) : $this->content();
    }

    public function content() {
      $template = $this->find_view_extention($this->view_path.DIRECTORY_SEPARATOR.$this->controller.DIRECTORY_SEPARATOR.$this->action);
      if ($template) $this->render($template[0],$template[1]);
      else throw new RoutingException('Template for action "'.$this->action.'" of controller "'.$this->controller.'" not found');
    }

    public function render($filename, $ext = 'php') { include($filename); }

    public function _include($path, $global = false) {
      $parts = array($global ? Template::LAYOUT_FOLDER : $this->controller, $path);
      if (strpos($path,'/') !== false)
        $parts = explode('/',$path,2);
      $parts[1] = '_'.$parts[1];

      $included = $this->find_view_extention($this->view_path.$parts[0].DIRECTORY_SEPARATOR.$parts[1]);
      if (!$included) throw new RoutingException('Partial template '.$path.' not found (global: '.($global ? 'true' : 'false').')');
      $this->render($included[0],$included[1]);
    }
  }
?>
