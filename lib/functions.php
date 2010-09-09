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
  
  function obj_serialize($obj) { return str_replace("\0", "~~NULL_BYTE~~", serialize($obj)); }
  function obj_unserialize($str) { return unserialize(str_replace("~~NULL_BYTE~~", "\0", $str)); }
  function singleton_cache_file($var) { return ROOT_DIR.'cache'.DIRECTORY_SEPARATOR.(is_string($var) ? $var : get_class($var)); }

  class SingletonesFactory {
    public static $_singletones = array();

    public static function factory($type) {
      if (true !== include_once(ROOT_DIR.'lib'.DIRECTORY_SEPARATOR.$type.'.php')){
        $cache_file_name = singleton_cache_file($type);
        $cache_exists = file_exists($cache_file_name) && is_file($cache_file_name) && is_readable($cache_file_name);
        self::$_singletones[$type] = $cache_exists ? obj_unserialize(file_get_contents($cache_file_name)) : (new $type);
        if ($cache_exists) self::$_singletones[$type]->readonly();
      }
      if (!array_key_exists($type, self::$_singletones))
        throw new Exception('Singleton not found');
      return self::$_singletones[$type];
    }
  }

  function cache_obj($obj) {
    $file_name = singleton_cache_file($obj);
    if (is_writable($file_name))
      file_put_contents($file_name,obj_serialize($obj));
  }
?>
