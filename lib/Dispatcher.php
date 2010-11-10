<?php
  class Dispatcher {
    protected static $route_params;
    protected static $layout;
    protected static $template;
    protected static $params;
    protected static $contoller;

    public static function config() {
      $c = get_config();

      if (!$c->is_readonly()) {
        $c->set('ROOT_DIR', ROOT_DIR);
        $c->set('CONFIG_DIR', ROOT_DIR.'config'.DIRECTORY_SEPARATOR);

        foreach (sfYaml::load(get_config('CONFIG_DIR').'base.yml') as $k => $v)
          $c->set(strtoupper($k),$v);
      }

      Template::setCSS($c->get('BASE_PATH').'css/');
      Template::setJS($c->get('BASE_PATH').'js/');
      Template::setIMG($c->get('BASE_PATH').'img/');
      Template::setICO($c->get('BASE_PATH'));
    }

    public static function routing() {
      $uri = parse_url($_SERVER['REQUEST_URI']);
      self::$route_params = get_routes()->checkout($uri['path']);
      if (!self::$route_params) get_header()->page404();
    }

    public static function control() {
      $h = get_header();

      if (!$h->is404()) {
        if (!array_key_exists('controller',self::$route_params))
          throw new RoutingException('Controller was not set in current routing');
        if (!array_key_exists('action',self::$route_params))
          self::$route_params['action'] = 'index';
        self::$params = array_merge($_GET,$_POST,self::$route_params); //TODO: add $_COOKIE and, maybe, $_SESSION

        $controller_name = self::$params['controller'];
        $controller = ucfirst(self::$params['controller']).'Controller';
        $action = self::$params['action'];
        unset(self::$params['controller']);
        unset(self::$params['action']);

        if (!class_exists($controller)) throw new RoutingException('Controller "'.$controller.'" was not found');
        if (!is_callable(array($controller, $action))) throw new RoutingException('Action "'.$action.'" was not found in controller "'.$controller.'"');

        self::$contoller = new $controller(self::$params);
        try { self::$contoller->$action(); } catch (Exception $e) {
          throw new BaseException('Uncaught exception in action "'.$action.'" of controller "'.$controller.'": '.$e->getMessage());
        }

        if (!$h->is404()) {
          self::$layout = self::$contoller->layout();
          if (is_null(self::$layout)) self::$layout = $controller_name;
          if (self::$layout === false) self::$layout = array($controller_name);
          self::$template = self::$contoller->template();
          if (is_null(self::$template)) self::$template = $action;
        } else {
          self::$layout = '404';
          self::$template = 'index';
        }
      }else
        self::$contoller = new ApplicationController(array());

      if ($h->is404()){
        self::$layout = '404';
        self::$template = 'index';
      }
    }

    public static function render() {
      $t = new ApplicationTemplate(self::$layout,self::$template,self::$contoller->get());
      get_header()->send_headers();
      $t->layout();
    }

    public static function finalize() {
      cache_obj(get_config());
      cache_obj(get_routes());
    }
  }
?>
