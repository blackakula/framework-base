<?php
//  echo htmlspecialchars($_SERVER['REQUEST_URI'])."<br />\n";
//  echo htmlspecialchars($_SERVER['QUERY_STRING'])."<br />\n";

  /* Load Config */
  require_once('../config/example_config.php');
  $c = get_config();
  foreach (sfYaml::load(get_config('CONFIG_DIR').'base.yml') as $k => $v)
    $c->set(strtoupper($k),$v);
//  var_dump($c);

  /* Load Routes */
  require_once(get_config('CONFIG_DIR').'routes.php');

?>
