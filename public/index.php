<?php
  echo htmlspecialchars($_GET['__path__'])."<br />\n";
  echo htmlspecialchars($_SERVER['QUERY_STRING'])."<br />\n";
  require_once('../config/example_config.php');
  $d = get_config();
  echo $c->get('ROOT_DIR').'<br />';
  echo $d->get('ROOT_DIR').'<br />';
  echo get_config()->get('ROOT_DIR').'<br />';
  echo get_config('ROOT_DIR').'<br />';
?>
