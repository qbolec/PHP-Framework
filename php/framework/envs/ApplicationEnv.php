<?php
class ApplicationEnv extends RequestEnv implements IApplicationEnv
{
  private $store = array();
  protected function get_types(){
    return array(
      self::DATA => 'IDataEnv',
    );
  }
  public function set($key,$value){
    $type = Arrays::grab($this->get_types(),$key);
    $this->halt_unless(gettype($value)==$type || ($value instanceof $type));
    $this->store[$key] = $value;
  }
  public function grab($key){
    return Arrays::grab($this->store,$key);
  }
  private function halt_unless($b){
    if(!$b){
      return Framework::get_instance()->get_assertions()->halt_unless($b);
    }
  }
}
?>
