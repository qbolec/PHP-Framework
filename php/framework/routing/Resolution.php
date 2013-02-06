<?php
class Resolution implements IResolution
{
  private $handler;
  private $env;
  public function __construct(IHandler $handler,IRequestEnv $env){
    $this->handler = $handler;
    $this->env = $env;
  }
  public function get_handler(){
    return $this->handler;
  }
  public function get_env(){
    return $this->env;
  }
}
?>
