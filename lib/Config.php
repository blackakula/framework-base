<?php
  class Config extends Hash {
    public function set($key, $value) { if ($this->is_readonly()) return; parent::set($key,$value); }
    public function del($key) {}
  }
?>
