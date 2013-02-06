<?php
interface IRequestFactory
{
  public function from_globals();
  public function from_method_url_post_data($method,$url,array $post_data);
}
?>
