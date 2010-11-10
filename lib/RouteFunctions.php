<?php
	class RouteFunctions {
		private static $_functions = array();
		
		public static function exists($name) {
			if (array_key_exists($name, self::$_functions)) return self::$_functions[$name];
			return false;
		}
		
		public static function set($name, $body, $params = '$params=array()') {
			self::$_functions[$name] = create_function($params,$body);
			return self::$_functions[$name];
		}
	}
?>