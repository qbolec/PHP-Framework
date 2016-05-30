<?php
require_once 'simple/test_bootstrap.php';
//1. chcę móc stworzyć isntancje różnych klas 
require 'autoload.php';
$r = new ReflectionClass('MockableSingleton');
my_assert($r->implementsInterface('IGetInstance'));
my_assert($r->implementsInterface('ISetInstance'));
class A extends MockableSingleton{
}
class B extends MockableSingleton{
}
class C extends B{
}
$a = A::get_instance();
$b = B::get_instance();
$c = C::get_instance();
my_assert(get_class($a)=='A');
my_assert(get_class($b)=='B');
my_assert(get_class($c)=='C');

//2. chcę móc podmienić instancję
$old_b = B::set_instance($c);
my_assert($old_b == $b);
$b2=B::get_instance();
my_assert(get_class($b2)=='C');
my_assert($b2===$c);
//3. chcę by ponowne wywoływanie funkcji zwracało to samo:
$a2 = A::get_instance();
my_assert($a===$a2);
$b3 = B::get_instance();
my_assert($b3===$b2);
my_assert($b3!==$b);
//4. chcę móc podmieniać instancje na null
A::set_instance(null);
my_assert(A::get_instance() instanceof A);
?>
