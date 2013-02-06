<?php
interface IPostJSONHandler extends IPostHandler
{
  public function get_post_validator(IApplicationEnv $env);
  public function get_post_data(IApplicationEnv $env);
}
?>
