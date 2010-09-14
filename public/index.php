<?php
  /* Load Config */
  require_once('../config/setup.php');
  $c = get_config();
  if (!$c->is_readonly()) {
    $c->set('ROOT_DIR', ROOT_DIR);
    $c->set('CONFIG_DIR', ROOT_DIR.'config'.DIRECTORY_SEPARATOR);

    foreach (sfYaml::load(get_config('CONFIG_DIR').'base.yml') as $k => $v)
      $c->set(strtoupper($k),$v);

    $dhandle = opendir(get_config('CONFIG_DIR'));
    while ($fname = readdir($dhandle)) {
      if (preg_match('/\\.yml$/',$fname) === 0 || $fname === 'base.yml') continue;
      $config_name = substr($fname,0,strlen($fname)-4);
      $c->set($config_name,sfYaml::load(get_config('CONFIG_DIR').$fname));
    }
  }

  $h = get_header();

  /* Load Routes and setting params */
  require_once(get_config('CONFIG_DIR').'routes.php');
  $uri = parse_url($_SERVER['REQUEST_URI']);
  $r = get_routes();
  $route_params = $r->checkout($uri['path']);
  if (!$route_params) $h->page404();

  if (!$h->is404()) {
    if (!array_key_exists('controller',$route_params))
      throw new RoutingException('Controller was not set in current routing');
    if (!array_key_exists('action',$route_params))
      $route_params['action'] = 'index';
    $params = array_merge($_GET,$_POST,$route_params);

    $controller_name = $params['controller'];
    $controller = ucfirst($params['controller']).'Controller';
    $action = $params['action'];
    unset($params['controller']);
    unset($params['action']);

    if (!class_exists($controller)) throw new RoutingException('Controller "'.$controller.'" was not found');
    if (!is_callable(array($controller, $action))) throw new RoutingException('Action "'.$action.'" was not found in controller "'.$controller.'"');

    $c = new $controller($params);
    try { $c->$action(); } catch (Exception $e) {
      throw new BaseException('Uncaught exception in action "'.$action.'" of controller "'.$controller.'": '.$e->getMessage());
    }

    if (!$h->is404()) {
      $layout = $c->layout();
      if (is_null($layout)) $layout = $controller_name;
      if ($layout === false) $layout = array($controller_name);
      $template = $c->template();
      if (is_null($template)) $template = $action;

    } else {
      $layout = '404';
      $template = 'index';
    }
  }else
    $c = new ApplicationController(array());

  if ($h->is404()){
    $layout = '404';
    $template = 'index';
  }

  $t = new ApplicationTemplate($layout,$template,$c->get());
  $h->send_headers();
  $t->layout();

  cache_obj(get_config());
  cache_obj(get_routes());
?>
