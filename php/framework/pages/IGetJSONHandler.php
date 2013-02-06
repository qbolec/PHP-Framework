<?php
interface IGetJSONHandler extends IGetHandler
{
  public function get_get_validator(IApplicationEnv $env);
  public function get_get_data(IApplicationEnv $env);
}
?>
