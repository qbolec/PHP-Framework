<?php
require_once 'simple/test_bootstrap.php';
//tutaj chce tylko przetestować, że istnieje sobie klasa framework
require 'autoload.php';
$f = Framework::get_instance();
my_assert($f instanceof Framework);
my_assert($f instanceof IFramework);
?>
