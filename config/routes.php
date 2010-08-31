<?php
  $r = get_routes();
  if (!$r->is_readonly()) {
    $r->connect('/',array('controller' => 'home'));
  }
?>
