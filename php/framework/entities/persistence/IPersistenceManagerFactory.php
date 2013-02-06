<?php
interface IPersistenceManagerFactory
{
  public function get_merged_with_cache(IPersistenceManager $base,IPersistenceManager $extension,IPrefetchingCache $cache,$key_prefix);
  public function from_config_name_and_descriptor($name, IFieldsDescriptor $fields_descriptor);
}
?>
