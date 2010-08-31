<?php
  /* Load Config */
  require_once('../config/example_config.php');
  $c = get_config();
  $c->set('ROOT_DIR', ROOT_DIR);
  $c->set('CONFIG_DIR', ROOT_DIR.'config'.DIRECTORY_SEPARATOR);
  foreach (sfYaml::load(get_config('CONFIG_DIR').'base.yml') as $k => $v)
    $c->set(strtoupper($k),$v);

  /* Load Routes */
  require_once(get_config('CONFIG_DIR').'routes.php');
  $uri = parse_url($_SERVER['REQUEST_URI']);
  $r = get_routes();
  $params = $r->checkout($uri['path']);
  if (!array_key_exists('controller',$params))
    throw new RoutingException('Controller was not set in current routing');
  if (!array_key_exists('action',$params))
    $params['action'] = 'index';

  cache_obj(get_config());
  cache_obj(get_routes());
  echo '<pre>';var_dump($params);echo '</pre>';
?>
