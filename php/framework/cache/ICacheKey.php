<?php
interface ICacheKey
{
  public function get();
  public function set($value);
  public function add($value);
  public function increment($delta);
  public function delete();
  public function increment_or_add($delta,$fallback_value);
  public function prefetch();
}
?>
