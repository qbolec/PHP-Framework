<?php
abstract class Handler implements IHandler, IPathResolver
{
  public function resolve_path(array $path,IRequestEnv $env){
    if(0!==count($path)){
      throw new HTTPNotFoundException($env);
    }else{
      return new Resolution($this,$env);
    }
  }
}
?>
