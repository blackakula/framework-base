<?php
  define('ROOT_DIR', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
  /* Load Config */
  require_once(ROOT_DIR.'config'.DIRECTORY_SEPARATOR.'setup.php');

  MyDispatcher::config();

  /* Load Routes and setting params */
  if (!get_routes()->is_readonly())
    require_once(get_config('CONFIG_DIR').'routes.php');

  MyDispatcher::routing();
  MyDispatcher::control();
  MyDispatcher::render();
  MyDispatcher::finalize();
?>
