<?php
interface IPathResolver{
  /**
   * @returns array('handler'=>IHandler,'env'=>IRequestEnv)
   */
  public function resolve_path(array $path,IRequestEnv $env);
}
?>
