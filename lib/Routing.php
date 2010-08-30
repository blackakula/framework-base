<?php
  class Routing {
    private $_routes;
    private $_functions;

    public function __construct() {
      $this->_routes = array();
      $this->_functions = array();
    }

    private function parse_path(&$result_vars, $path = '/', &$parts = null) {
      if ($path[0] === '/') $path = substr($path,1);

      $result_vars = array();
      $str = $path;
      $regexp_str = '';
      $parts = array('/');
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
      
      return '^/'.$regexp_str.'$';
    }

    private function _connect($path, $params = array(), &$parts = null) {
      $route = new Route($this->parse_path($vars,$path,$parts));
      $route->set_params($vars,$params);
      array_push($this->_routes,$route);
//      echo '<pre>';
//      var_dump($route);
//      echo "</pre>";
    }

    private function _build_url($parts) {
      $body = array();
      foreach ($parts as &$v) {
        $string_param = "'".str_replace("'","\\'",is_string($v) ? $v : $v[0])."'";
        array_push($body, is_string($v) ? $string_param : '(array_key_exists('.$string_param.',$params) ? $params['.$string_param.'] : "")');
      }
      return 'return '.implode('.',$body).';';
    }

    public function connect($path, $params = array()) { $this->_connect($path,$params); }
    public function named($name, $path, $params = array()) {
      $this->_connect($path,$params,$parts);
      $this->_functions[$name] = create_function('$params=array()',$this->_build_url($parts));
    }

    public function build($name, $params = array()) {
      return array_key_exists($name,$this->_functions) ? $this->_functions[$name]($params) : null;
    }
  }
?>
