<?php
interface IPrefetchingCache extends ICache
{
  public function prefetch($key_name);
}
?>
