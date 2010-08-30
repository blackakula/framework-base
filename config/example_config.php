<?php
  define('ROOT_DIR', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
  require_once(ROOT_DIR.'lib'.DIRECTORY_SEPARATOR.'functions.php');

  $c = get_config();
  $c->set('ROOT_DIR', ROOT_DIR);
  $c->set('CONFIG_DIR', ROOT_DIR.'config'.DIRECTORY_SEPARATOR);
?>
