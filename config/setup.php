<?php
  function __autoload($class_name) {
    $lib_dir = ROOT_DIR.'lib'.DIRECTORY_SEPARATOR;
    $filename = $class_name.'.php';
    $full_name = $lib_dir.'my'.DIRECTORY_SEPARATOR.$filename;

    if (in_array($class_name, array('Config','Routing','HttpHeader'))) return;
    elseif (is_readable($full_name));
    elseif ($class_name == 'ApplicationTemplate')
      $full_name = ROOT_DIR.'app'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$filename;
    elseif ($class_name == 'sfYaml')
      $full_name = $lib_dir.'sfYaml'.DIRECTORY_SEPARATOR.$filename;
    elseif (preg_match('/.Controller$/',$class_name) !== 0)
      $full_name = ROOT_DIR.'app'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$filename;
    elseif (preg_match('/.Exception$/',$class_name) !== 0)
      $full_name = $lib_dir.'exceptions'.DIRECTORY_SEPARATOR.$filename;

    if (!is_readable($full_name)) $full_name = $lib_dir.$filename;
    if (is_readable($full_name)) require_once($full_name);
  }
  
  function get_config($param = null) {
    $config = SingletonesFactory::factory('Config');
    return ($param === null) ? $config : $config->get($param);
  }
  function get_routes() { return SingletonesFactory::factory('Routing'); }
  function get_header() { return SingletonesFactory::factory('HttpHeader'); }
  
  function obj_serialize($obj) { return str_replace("\0", "~~NULL_BYTE~~", serialize($obj)); }
  function obj_unserialize($str) { return unserialize(str_replace("~~NULL_BYTE~~", "\0", $str)); }
  function singleton_cache_file($var) {
  	$cache_dir = ROOT_DIR.'cache';
  	return is_writable($cache_dir) ? $cache_dir.DIRECTORY_SEPARATOR.(is_string($var) ? $var : get_class($var)) : null;
  }

  class SingletonesFactory {
    public static $_singletones = array();

    public static function &factory($type) {
      if (true !== include_once(ROOT_DIR.'lib'.DIRECTORY_SEPARATOR.$type.'.php')){
        $cache_file_name = singleton_cache_file($type);
        $cache_exists = is_readable($cache_file_name);
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
    if (!is_null($file_name))
      file_put_contents($file_name,obj_serialize($obj));
  }
?>
