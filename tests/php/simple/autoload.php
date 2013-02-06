<?
include_once('test_bootstrap.php');
//ta klasa ma przetestować jedynie to, czy działa autoload
//w tym celu załadujemy klasę Framework (która mam nadzieje istnieje)
//1. mam nadzieję, że autoload.php jest na include path
require_once 'autoload.php';
//2. mam nadzieję, że umie wgrać klasę framework
my_assert(class_exists('Framework',true));
?>
