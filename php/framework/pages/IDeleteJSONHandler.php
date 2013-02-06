<?php
interface IDeleteJSONHandler extends IDeleteHandler
{
  public function get_delete_validator(IApplicationEnv $env);
  public function get_delete_data(IApplicationEnv $env);
}
?>
