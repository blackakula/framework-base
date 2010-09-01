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

  /* Load Routes and setting params */
  require_once(get_config('CONFIG_DIR').'routes.php');
  $uri = parse_url($_SERVER['REQUEST_URI']);
  $r = get_routes();
  $route_params = $r->checkout($uri['path']);
  if (!array_key_exists('controller',$route_params))
    throw new RoutingException('Controller was not set in current routing');
  if (!array_key_exists('action',$route_params))
    $route_params['action'] = 'index';
  $params = array_merge($_GET,$_POST,$route_params);

  cache_obj(get_config());
  cache_obj(get_routes());
  echo '<pre>';var_dump($params);echo '</pre>';
?>
