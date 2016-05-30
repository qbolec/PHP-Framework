<?php
interface IPutJSONHandler extends IPutHandler
{
  public function get_put_validator(IApplicationEnv $env);
  public function get_put_data(IApplicationEnv $env);
}
?>
