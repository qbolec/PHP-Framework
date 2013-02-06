<?php
/**
 * note how this is totaly unrelated to ICache !
 * this is just to abstract the 3rd party component, namely Memcache
 */
interface IMemcache{
  //public function get($key_name, &$flags = null);
  public function get();
  //public function set($key_name,$value,$flags,$ttl);
  public function set();
  //public function add($key_name,$value,$flags,$ttl);
  public function add();
  //public function increment($key_name,$delta);
  public function increment();
  //public function decrement($key_name,$delta);
  public function decrement();
  //public function delete($key_name);
  public function delete();
  //public function addServer($host,$port);
  public function addServer();
}
?>
