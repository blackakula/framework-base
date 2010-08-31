<?php
  function __autoload($class_name) {
    $lib_dir = ROOT_DIR.'lib'.DIRECTORY_SEPARATOR;
    if (in_array($class_name, array('Config','Routing'))) return;
    elseif ($class_name == 'sfYaml')
      require_once($lib_dir.'sfYaml'.DIRECTORY_SEPARATOR.'sfYaml.php');
    else
      require_once($lib_dir.$class_name.'.php');
  }
  
  function get_config($param = null) {
    $config = SingletonesFactory::factory('Config');
    return ($param === null) ? $config : $config->get($param);
  }

  function get_routes() {
    return SingletonesFactory::factory('Routing');
  }

  class SingletonesFactory {
    public static $_singletones = array();

    public static function factory($type) {
      if (true !== include_once(ROOT_DIR.'lib'.DIRECTORY_SEPARATOR.$type.'.php'))
        self::$_singletones[$type] = new $type;
      if (!array_key_exists($type, self::$_singletones))
        throw new Exception('Singleton not found');
      return self::$_singletones[$type];
    }
}
?>
