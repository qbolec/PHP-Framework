<?php
interface IRouter{
  /**
   * @returns array('handler'=>IHandler,'env'=>IRequestEnv)
   */
  public function resolve(IRequestEnv $env);
}
?>
