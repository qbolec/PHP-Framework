<?php
class Framework extends MockableSingleton implements IFramework{
  public function get_logger(){
    return Logger::get_instance();
  }
  private $assertions = null;
  public function get_assertions(){
    if(null === $this->assertions){
      $this->assertions = Assertions::get_instance();
    }
    return $this->assertions;
  }
  public function get_rng(){
    return MTRNG::get_instance();
  }
  public function get_time(){
    return time();
  }
  public function get_server_info_factory(){
    return ServerInfoFactory::get_instance();
  }
  public function get_output(){
    return HTTPOutput::get_instance();
  }
  public function get_request_factory(){
    return RequestFactory::get_instance();
  }
  public function get_response_factory(){
    return ResponseFactory::get_instance();
  }
  public function get_pdo_factory(){
    return PDOFactory::get_instance();
  }
  private $sharding_factory;
  public function get_sharding_factory(){
    if(null === $this->sharding_factory){
      $this->sharding_factory = ShardingFactory::get_instance();
    }
    return $this->sharding_factory;
  }
  public function get_relation_manager_factory(){
    return RelationManagerFactory::get_instance();
  }
  public function get_persistence_manager_factory(){
    return PersistenceManagerFactory::get_instance();
  }
  public function get_cache_factory(){
    return PrefetchingCacheFactory::get_instance();
  }
  public function get_validation_exception_explainer_factory(){
    return ValidationExceptionExplainerFactory::get_instance();
  }
  public function get_tickets(){
    return Tickets::get_instance();
  }
  public function get_signatures(){
    return Signatures::get_instance();
  }
  public function get_cache_versioning_factory(){
    return CacheVersioningFactory::get_instance();
  }
  public function get_redis_factory(){
    return RedisFactory::get_instance();
  }
  public function get_lock_factory(){
    return LockFactory::get_instance();
  }
  public function get_templates(){
    return Templates::get_instance();
  }
}
?>
