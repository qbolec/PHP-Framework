<?php
class Router extends AbstractRouter
{
  protected $routing_table = array();
  protected function get_routing_table(){
    return $this->routing_table;
  }
  public function resolve_path(array $path,IRequestEnv $env){
    if(0==count($path)){
      $partial_resolution = $this->resolve_here($env);
    }else{
      $next = array_shift($path);
      $partial_resolution = $this->resolve_part($next,$env);
    }
    return $partial_resolution->get_resolver()->resolve_path($path,$partial_resolution->get_env());
  }
  protected function resolve_here(IRequestEnv $env){
    return $this->resolve_part('',$env);
  }
  protected function resolve_part($part,IRequestEnv $env){
    $routing_table = $this->get_routing_table();
    if(!array_key_exists($part,$routing_table)){
      throw new HTTPNotFoundException($env);
    }else{
      $resolver_class_name = $routing_table[$part];
      $resolver = new $resolver_class_name();
      return new PartialResolution($resolver,$env);
    }
  }
}
?>
