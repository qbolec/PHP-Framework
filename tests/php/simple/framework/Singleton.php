<?php
require_once 'simple/test_bootstrap.php';
require 'autoload.php';
$r = new ReflectionClass('Singleton');
my_assert($r->implementsInterface('IGetInstance'));
//testy dla Singletona
class A extends Singleton
{
}
class B extends Singleton
{
}
class C extends B
{
}
//1. każdy singleton ma co najwyżej jedną kopię
$a=A::get_instance();
my_assert($a === A::get_instance());
my_assert($a instanceof A);
$b=B::get_instance();
my_assert($b === B::get_instance());
my_assert($b instanceof B);
$c=C::get_instance();
my_assert($c === C::get_instance());
my_assert($c instanceof C);
//2. nie chcę by coś się pomieszało
my_assert($a !== $b);
my_assert($b !== $c);
my_assert($c !== $a);

?>
