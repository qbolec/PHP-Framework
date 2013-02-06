<?php
class PersistenceManagerFactory extends AbstractTableManagerFactory implements IPersistenceManagerFactory
{
  private function wrap_with_caching(IPersistenceManager $far,IPrefetchingCache $cache,$key_prefix){
    $near = new SimpleCachePersistenceManager($far->get_fields_descriptor(),$cache,$key_prefix);
    return $this->wrap_with_prefetching(
      new LayeredPersistenceManager($near,$far)
    );
  }
  public function get_merged_with_cache(IPersistenceManager $base,IPersistenceManager $extension,IPrefetchingCache $cache,$key_prefix){
    $merged = new MergedPersistenceManager($base,$extension);
    return $this->wrap_with_caching($merged,$cache,$key_prefix);
  }
  private function wrap_with_prefetching(IPersistenceManager $pm){
    return new PrefetchingPersistenceManagerWrapper($pm);
  }
  protected function get_type_to_factory(){
    return array(
      'layered' => 'LayeredPersistenceManagerFactory',
      'pdo' => 'SimplePDOPersistenceManagerFactory',
      'cache' => 'SimpleCachePersistenceManagerFactory',
      'versioned' => 'VersionedPersistenceManagerFactory',
    );
  }
  protected function get_path($name){
    return "entities/$name";
  }
  protected function wrap_creation($creation){
    return $this->wrap_with_prefetching($creation);
  }
}
?>
