<?php
class CachedRelationManagerFactory extends MultiInstance implements IConfigurableRelationManagerFactory
{
  public function from_config_and_descriptor(IRelationManagerFactory $factory,array $config,IFieldsDescriptor $descriptor){
    $inner = $factory->from_config_name_and_descriptor(Arrays::grab($config,'inner'),$descriptor);
    $cache_factory = Framework::get_instance()->get_cache_factory();
    $cache = $cache_factory->get_cache(Arrays::grab($config,'cache'));
    $prefix = Arrays::grab($config,'prefix');
    $versioning_name = Arrays::get($config,'versioning');
    $cvf = Framework::get_instance()->get_cache_versioning_factory();
    if(null===$versioning_name){
      $versioning_cache_name = Arrays::get($config,'versioning_cache');
      if(null===$versioning_cache_name){
        $versioning_cache = $cache;
      }else{
        $versioning_cache = $cache_factory->get_cache($versioning_cache_name);
      }
      $versioning = $cvf->from_cache_prefix_and_descriptor($versioning_cache,$prefix,$descriptor);
    }else{
      $versioning = $cvf->from_config_name($versioning_name);
    }  
    return new CachedRelationManager($cache,$versioning,$prefix,$inner);
  }
}
?>
