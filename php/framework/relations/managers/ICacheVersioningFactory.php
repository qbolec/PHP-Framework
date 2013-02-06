<?php
interface ICacheVersioningFactory
{
  public function from_config_name($name);
  public function from_cache_prefix_and_descriptor(IPrefetchingCache $cache,$prefix,IFieldsDescriptor $descriptor);
}
?>
