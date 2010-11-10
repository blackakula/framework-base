<?php
  class Config extends Hash {
    public function set($key, $value) { if ($this->is_readonly()) return; parent::set($key,$value); }
    public function del($key) {}
  	public function &get($key = null) {
  		if ($key === null) return $this->_params;
  		if (array_key_exists($key, $this->_params)) return $this->_params[$key];
  		
  		$f_name = $this->_params['CONFIG_DIR'].$key.'.yml';
  		if (is_readable($f_name)) {
  			parent::set($key, sfYaml::load($f_name));
  			return $this->_params[$key];
  		}
  		return null;
  	}
  }
?>
