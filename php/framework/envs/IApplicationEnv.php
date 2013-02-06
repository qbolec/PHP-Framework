<?php
interface IApplicationEnv extends IRequestEnv
{
  const DATA = 0;
  public function set($key,$value);
  public function grab($key);
}
?>
