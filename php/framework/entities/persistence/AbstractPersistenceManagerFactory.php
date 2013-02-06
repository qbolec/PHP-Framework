<?php
abstract class AbstractPersistenceManagerFactory extends MultiInstance
{
  protected function get_cache_factory(){
    return Framework::get_instance()->get_cache_factory();
  }
  protected function get_persistence_manager_factory(){
    return Framework::get_instance()->get_persistence_manager_factory();
  }
  protected function get_sharding_factory(){
    return Framework::get_instance()->get_sharding_factory();
  }
  protected function get_none_sharding(){
    return $this->get_sharding_factory()->get_none();
  }
}
?>
