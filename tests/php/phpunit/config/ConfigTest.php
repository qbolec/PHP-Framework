<?php
class ConfigTest extends PHPUnit_Framework_TestCase
{
  public function testSingleton(){
    $c1 = Config::get_instance();
    $c2 = Config::get_instance();
    $this->assertSame($c1,$c2);
  }
  public function testInterface(){
    $c = Config::get_instance();
    $this->assertInstanceOf('IConfig',$c);
    $this->assertInternalType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY,$c->get(''));
  }
  private function getConfigValidator(){
    return new RecordValidator(array(
      'nk' => new RecordValidator(array(
        'app_id' => new IntValidator(),
        'static_base_uri' => new StringValidator(),
        'api' => new RecordValidator(array(
          'max_recipients_per_notification' => new IntValidator(),
        )),
      )),
      'modules' => new RecordValidator(array(
      )),
      'signatures' => new RecordValidator(array(
        'secret' => new StringValidator(),
      )),
      'tickets' => new RecordValidator(array(
        'authentication' => new RecordValidator(array(
          'ttl' => new IntValidator(),
        )),
      )),
      'oauth' => new RecordValidator(array(
        'key' => new StringValidator(),
        'secret' => new StringValidator(),
      )),
      'versionings' => new MapValidator(new StringValidator(),new RecordValidator(array(
        'cache' => new StringValidator(),
        'prefix' => new StringValidator(),
        'columns' => new ArrayValidator(new StringValidator()),
      ))),
      'logging' => new RecordValidator(array(
        'rules' => new MapValidator(new IsPCREValidator(), new RecordValidator(array(
          'verbosity' => new IntValidator(),
          'priority' => new OptionalValidator(new IntValidator()),
        ))),
      )),
      'entities' => new MapValidator(new StringValidator(),new RecordValidator(array(
        'type' => new StringValidator(),
        'config' => new AnythingValidator(),
      ))),
      'relations' => new MapValidator(new StringValidator(),new RecordValidator(array(
        'type' => new StringValidator(),
        'config' => new AnythingValidator(),
      ))),
      'shardings'=> new MapValidator(new StringValidator(),new RecordValidator(array(
        'type' => new StringValidator(),
        'config' => new AnythingValidator(),
      ))),
      'pdos' => new RecordValidator(array(
        'users' => new MapValidator(new StringValidator(),new RecordValidator(array(
          'username' => new StringValidator(),
          'password' => new StringValidator(),
        ))),
        'endpoints' => new MapValidator(new StringValidator(),new RecordValidator(array(
          'host' => new StringValidator(),
          'port' => new IntValidator(),
        ))),
        'masters' => new MapValidator(new StringValidator(),new ArrayValidator(new RecordValidator(array(
          'dsn' => new StringValidator(),//RegexpValidator
          'user' => new StringValidator(),
          'endpoint' => new StringValidator(),
        )))),
      )),
      'caches' => new MapValidator(new StringValidator(),new RecordValidator(array(
        'type' => new StringValidator(),//OneOfValidator
        'config' => new AnythingValidator(),
      ))),
      'memcaches' => new RecordValidator(array(
        'clusters' => new MapValidator(new StringValidator(),new RecordValidator(array(
          'servers' => new ArrayValidator(new RecordValidator(array(
            'port'=> new IntValidator(),
            'host'=> new StringValidator(),
          ))),
        ))),
      )),
      'redises' => new MapValidator(new StringValidator(),new RecordValidator(array(
        'type' => new StringValidator(),
        'config' => new AnythingValidator(),
      ))), 
      'js' => new RecordValidator(array(
        'uris' => new RecordValidator(array(
          'communicationFrame' => new StringValidator(),
          'login' => new StringValidator(),
          'baseUri' => new StringValidator(),
          'nkAppUri' => new StringValidator(),
        )),  
      )),
    ));
  }
  public function testShape(){
    $c = Config::get_instance();
    $v = $this->getConfigValidator();
    $e=$v->get_error($c->get(''));
    if($e!==null){
      $explainer = Framework::get_instance()->get_validation_exception_explainer_factory()->get_dev();
      echo $explainer->explain($e);
      $this->fail();
    }
  }
  public function testCaches(){
    $c = Config::get_instance();
    $caches_names = array_keys($c->get('caches'));
    $cache_factory = Framework::get_instance()->get_cache_factory();
    foreach($caches_names as $cache_name){
      $cache = $cache_factory->get_cache($cache_name);
      $this->assertInstanceOf('ICache',$cache);
    }
  }
  public function testRelations(){
    $c = Config::get_instance();
    $relations_names = array_keys($c->get('relations'));
    $relation_manager_factory = Framework::get_instance()->get_relation_manager_factory();
    $fd=FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));
    foreach($relations_names as $relation_name){
      $relation_manager = $relation_manager_factory->from_config_name_and_descriptor($relation_name,$fd);
      $this->assertInstanceOf('IRelationManager',$relation_manager);
    }
  }
  public function testEntities(){
    $c = Config::get_instance();
    $entities_names = array_keys($c->get('entities'));
    $persistence_manager_factory = Framework::get_instance()->get_persistence_manager_factory();
    $fd=FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));
    foreach($entities_names as $entity_name){
      $entity_manager = $persistence_manager_factory->from_config_name_and_descriptor($entity_name,$fd);
      $this->assertInstanceOf('IPersistenceManager',$entity_manager);
    }
  }
  public function testRedises(){
    $c = Config::get_instance();
    $redises_names = array_keys($c->get('redises'));
    $redis_factory = Framework::get_instance()->get_redis_factory();
    foreach($redises_names as $redis_name){
      $redis = $redis_factory->get_redis($redis_name);
      $this->assertInstanceOf('IRedisDB',$redis);
    }
  }
  public function testShardings(){
    $c = Config::get_instance();
    $names = array_keys($c->get('shardings'));
    $factory = Framework::get_instance()->get_sharding_factory();
    foreach($names as $name){
      $sharding = $factory->from_config_name($name);
      $this->assertInstanceOf('ISharding',$sharding);
    }
  }
  public function testVersionings(){
    $c = Config::get_instance();
    $names = array_keys($c->get('versionings'));
    $factory = Framework::get_instance()->get_cache_versioning_factory();
    foreach($names as $name){
      $versioning = $factory->from_config_name($name);
      $this->assertInstanceOf('ICacheVersioning',$versioning);
    }
  }
  public function getPDOs(){
    $pdos = array();
    $c = Config::get_instance();
    $cs = $c->get('pdos/masters');
    foreach($cs as $name=>$shards){
      foreach(array_keys($shards) as $shard_id){
        $pdos[] = array($name,$shard_id);
      }
    }
    return $pdos;
  }
  /**
   * @dataProvider getPDOs
   */
  public function testPDOs($name,$shard_id){
    $factory = Framework::get_instance()->get_pdo_factory();
    $pdo = $factory->get_pdo($name,$shard_id);
    $this->assertInstanceOf('IPDO',$pdo);
  }
}
?>
