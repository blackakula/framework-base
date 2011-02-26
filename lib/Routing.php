<?php
  class Routing extends SingletonObj {
    private $_routes;
    private $_functions;

    public function __construct() {
      $this->_routes = array();
      $this->_functions = array();
      parent::__construct();
    }

    private function parse_path(&$result_vars, $path = '/', &$parts = null) {
      if ($path[0] === '/') $path = substr($path,1);
      while (false !== ($pos = strpos($path,'(')) && substr($path,$pos,3) !== '(?:')
        $path = substr($path,0,$pos).'(?:'.substr($path,$pos+1);

      $result_vars = array();
      $str = $path;
      $regexp_str = '';
      $parts = array();
      preg_match_all('/\\:[a-z_]+/',$path,$variables);
      foreach ($variables[0] as &$var) {
        $str_parts = explode($var,$str,2);

        $regexp_str .= $str_parts[0].'([^/\\?\\.]*)';
        $str = $str_parts[1];
        array_push($parts,$str_parts[0]);
        array_push($parts,array($var,sizeof($result_vars)));
        array_push($result_vars,$var);
      }
      $regexp_str .= $str;
      if (!empty($str)) array_push($parts,$str);
      
      return '^'.get_config('BASE_PATH').$regexp_str.'$';
    }

    private function _connect($path, $params = array(), &$parts = null) {
      if ($this->is_readonly()) return;
      $route = new Route($this->parse_path($vars,$path,$parts));
      $route->set_params($vars,$params);
      array_push($this->_routes,$route);
    }

    private function _create_url($parts, $path = false) {
      $body = array("'".get_config($path ? 'BASE_PATH' : 'ROOT_URL')."'");
      foreach ($parts as &$v) {
        $string_param = "'".str_replace("'","\\'",is_string($v) ? $v : $v[0])."'";
        array_push($body, is_string($v) ? $string_param : '(array_key_exists('.$string_param.',$params) ? $params['.$string_param.'] : "")');
      }
      return 'return '.implode('.',$body).';';
    }
    private function _create_url_function($name, $parts, $path = false) {
      if ($this->is_readonly()) return;
      $this->_functions[$name] = $this->_create_url($parts,$path);
    }

    public function connect($path, $params = array()) { $this->_connect($path,$params); }
    public function named($name, $path, $params = array()) {
      $this->_connect($path,$params,$parts);
      $this->_create_url_function($name.'_url',$parts);
      $this->_create_url_function($name.'_path',$parts,true);
    }

    private function _build($name, $params = array()) {
      if (!array_key_exists($name,$this->_functions))
        throw new RoutingException('Tried to build undefined route');
      if ($f = RouteFunctions::exists($name)) return $f($params);
      $f = RouteFunctions::set($name, $this->_functions[$name]);
      return $f($params);
    }

    public function build_url($name, $params = array()) { return $this->_build($name.'_url',$params); }
    public function build_path($name, $params = array()) { return $this->_build($name.'_path',$params); }

    public function checkout($url) {
      foreach ($this->_routes as &$route) {
        $result = $route->check($url);
        if ($result) return $result;
      }
      return false;
    }
  }
?>
