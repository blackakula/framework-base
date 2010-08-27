<?php
  define('ROOT_DIR', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
  require_once(ROOT_DIR.'lib/functions.php');

  $c = get_config();
  $c->set('ROOT_DIR', ROOT_DIR);
?>
