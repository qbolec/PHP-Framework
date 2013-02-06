<?php
class EmptyRouter extends AbstractRouter
{
  public function resolve_path(array $path,IRequestEnv $env){
    throw new HTTPNotFoundException($env);
  }
}
?>
