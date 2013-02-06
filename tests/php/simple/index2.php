<?php
include_once('test_bootstrap.php');
//kolejny test dla index.php, tym razem korzystając z możliwości mockowania
//Setup:
require_once 'autoload.php';
class FakeApplication extends AbstractApplication{
  public static $ok=0;
  public function get_config(){
    throw new LogicException();
  }
  protected function get_root_router(){
    throw new LogicException();
  }
  public function run(){
    self::$ok++;
  } 
}
BetterApplication::set_instance(FakeApplication::get_instance());
//Test:
require_once '../../../htdocs/index.php';
//Check: Sprawdzam, czy wowołało sie raz
my_assert(FakeApplication::$ok == 1);
?>
