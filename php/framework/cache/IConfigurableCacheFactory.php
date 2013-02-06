<?php
interface IConfigurableCacheFactory{
  /**
   * @return ICache
   */
  public function get_cache_from_config(ICacheFactory $factory,$config);
}
?>
