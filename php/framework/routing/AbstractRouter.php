<?php
abstract class AbstractRouter implements IRouter, IPathResolver
{
  public function resolve(IRequestEnv $env){
    $path = $env->get_request()->get_path();
    $path_parts = array_values(array_filter( explode('/',$path) , 'strlen' ));
    return $this->resolve_path($path_parts,$env);
  }  
}
?>
