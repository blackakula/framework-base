<?php
  function __autoload($class_name) {
    $lib_dir = ROOT_DIR.'lib'.DIRECTORY_SEPARATOR;
    $filename = $class_name.'.php';
    if (in_array($class_name, array('Config','Routing'))) return;
    elseif ($class_name == 'sfYaml')
      require_once($lib_dir.'sfYaml'.DIRECTORY_SEPARATOR.$filename);
    elseif (preg_match('/.Controller$/',$class_name) !== 0)
      require_once(ROOT_DIR.'app'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$filename);
    elseif (preg_match('/.Exception$/',$class_name) !== 0)
      require_once($lib_dir.'exceptions'.DIRECTORY_SEPARATOR.$filename);
    else
      require_once($lib_dir.$filename);
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
