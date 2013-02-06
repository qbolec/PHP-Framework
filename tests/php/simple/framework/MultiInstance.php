<?php
require_once 'simple/test_bootstrap.php';
require 'autoload.php';
$r = new ReflectionClass('MultiInstance');
my_assert($r->implementsInterface('IGetInstance'));
//testy dla MultiInstance
class A extends MultiInstance
{
}
$a1=A::get_instance();
my_assert($a1 instanceof A);
$a2=A::get_instance();
my_assert($a2 instanceof A);
my_assert($a2 !== $a1);
?>
