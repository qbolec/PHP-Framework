<?php
interface ICache{
  public function get($key_name);
  public function multi_get(array $key_names);
  public function set($key_name,$value);
  public function add($key_name,$value);
  public function increment($key_name,$delta);
  public function delete($key_name);
  public function increment_or_add($key_name,$delta,$fallback_value);
}
?>
