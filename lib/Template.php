<?php
  class Template extends Hash {

    const LAYOUT_FOLDER = 'layouts';
    protected $view_path;
    protected $controller;
    protected $action;
    protected $extentions;
    protected $check_template;
    protected $render_layout;

    protected static $_css = '/css/';
    protected static $_js = '/js/';
    protected static $_img = '/img/';
    protected static $_icon = '/';

    public function __construct($controller,$action,$params) {
      parent::__construct();
      $this->_params = $params;
      $this->view_path = get_config('ROOT_DIR').'app'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
      $this->render_layout = !is_array($controller);
      $this->controller = $this->render_layout ? $controller : $controller[0];
      $this->action = $action;
      $this->extentions = array('php');
    }

    private function find_view_extention($full_file_path_without_extention) {
      $view_file = false;
      foreach ($this->extentions as $extention){
        $filename = $full_file_path_without_extention.'.'.$extention;
        if (is_readable($filename)) {
          $mod_date = filemtime($filename);
          if (!$view_file || $mod_date > $view_file[2])
            $view_file = array($filename,$extention,$mod_date);
        }
      }
      return $view_file;
    }

    public function layout() {
      if (!$this->render_layout) return $this->content();
      $filepath = $this->view_path.(Template::LAYOUT_FOLDER).DIRECTORY_SEPARATOR;
      $layout = $this->find_view_extention($filepath.$this->controller);
      if (!$layout) $layout = $this->find_view_extention($filepath.'layout');
      $layout ? $this->render($layout[0],$layout[1]) : $this->content();
    }

    public function content() {
      if ($this->action === false) return;
      $template = $this->find_view_extention($this->view_path.$this->controller.DIRECTORY_SEPARATOR.$this->action);
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
      if (!$included && !$global)
        $this->_include($path,true);
      else {
        if (!$included) throw new RoutingException('Partial template '.$path.' not found (global: '.($global ? 'true' : 'false').')');
        $this->render($included[0],$included[1]);
      }
    }

    public static function setCSS($base_url) { self::$_css = $base_url; }
    public static function setJS($base_url) { self::$_js = $base_url; }
    public static function setIMG($base_url) { self::$_img = $base_url; }
    public static function setICO($base_url) { self::$_icon = $base_url; }

    public static function getIMG() { return self::$_img; }

    public function _css($src, $global = false, $media = 'screen') {
      if (!$global) $src = (self::$_css).$src;
      return '<link rel="stylesheet" href="'.htmlspecialchars($src).'" type="text/css" media="'.$media.'" />';
    }

    public function _js($src, $global = false) {
      if (!$global) $src = (self::$_js).$src;
      return '<script type="text/javascript" src="'.htmlspecialchars($src).'"></script>';
    }

    public function _img($src, $alt = null, $global = false, $attr = array()) {
      if (!$global) $src = (self::$_img).$src;
      if (is_null($alt)) {
        $pos_l = strrpos($src,'/');
        if ($pos_l === false) $pos_l = -1;
        $pos_r = strrpos($src,'.');
        if ($pos_r === false || $pos_r < $pos_l + 2) $pos_r = strlen($src);
        $alt = ucwords(strtolower(str_replace('_',' ',str_replace('-',' ',substr($src,$pos_l + 1,($pos_r-$pos_l-1))))));
      }
      $attr['src'] = $src;
      $attr['alt'] = $alt;
      foreach ($attr as $k => $v)
        $attr[$k] = $k.'="'.((substr($k,0,2) == 'on' || $k == 'style') ? $v : htmlspecialchars($v)).'"';
      return '<img '.implode(' ',$attr).' />';
    }

    public function _icon($src, $rel = 'Shortcut Icon', $global = false) {
      if (!$global) $src = (self::$_icon).$src;
      return '<link rel="'.htmlspecialchars($rel).'" type="image/x-icon" href="'.htmlspecialchars($src).'" />';
    }
  }
?>
