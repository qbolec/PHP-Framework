<?php
interface ICacheVersioning
{
  public function invalidate(array $key);
  public function get_version(array $key);
  public function get_versions(array $keys);
}
?>
