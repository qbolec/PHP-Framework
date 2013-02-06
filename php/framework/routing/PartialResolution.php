<?php
class PartialResolution implements IPartialResolution
{
  private $resolver;
  private $env;
  public function __construct(IPathResolver $resolver,IRequestEnv $env){
    $this->resolver = $resolver;
    $this->env = $env;
  }
  public function get_resolver(){
    return $this->resolver;
  }
  public function get_env(){
    return $this->env;
  }
}
?>
