<?php
class Assertions extends MultiInstance implements IAssertions
{
  public function halt_if($condition){
    if($condition){
      $this->halt();
    }
  }
  protected function halt(){
    throw new LogicException();
  }
  public function warn_if($condition){
    if($condition){
      $this->warn();
    }
  }
  public function halt_unless($condition){
    $this->halt_if(!$condition);
  }
  public function warn_unless($condition){
    $this->warn_if(!$condition);
  }
  protected function warn(){
    Framework::get_instance()->get_logger()->log();
  }
}
?>
