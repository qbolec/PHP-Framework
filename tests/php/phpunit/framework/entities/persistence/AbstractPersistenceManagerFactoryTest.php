<?php
abstract class AbstractPersistenceManagerFactoryImpl extends AbstractPersistenceManagerFactory
{
  public function get_cache_factory(){
    return parent::get_cache_factory();
  }
  public function get_persistence_manager_factory(){
    return parent::get_persistence_manager_factory();
  }
  public function get_sharding_factory(){
    return parent::get_sharding_factory();
  }
  public function get_none_sharding(){
    return parent::get_none_sharding();
  }
}
class AbstractPersistenceManagerFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = $this->getMockForAbstractClass('AbstractPersistenceManagerFactory');
    $this->assertInstanceOf('IGetInstance',$a);
  }
  public function testProtectedInterface(){
    $a = $this->getMockForAbstractClass('AbstractPersistenceManagerFactoryImpl');
    $this->assertInstanceOf('ICacheFactory',$a->get_cache_factory());
    $this->assertInstanceOf('IPersistenceManagerFactory',$a->get_persistence_manager_factory());
    $this->assertInstanceOf('IShardingFactory',$a->get_sharding_factory());
    $this->assertInstanceOf('ISharding',$a->get_none_sharding());

  }
}
?>
