<?php
class CachedRelationManagerFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new CachedRelationManagerFactory();
  }
  public function testInterface(){
    $crmf = $this->getSUT();
    $this->assertInstanceOf('IConfigurableRelationManagerFactory',$crmf);
  }
  public function testConfigWithoutVersioningName(){
    $crmf = $this->getSUT();
    $prefix = 'x';
    $inner_name = 'inner-name';
    $cache_name = 'cache-name';
    $descriptor = $this->getUserlikeFieldsDescriptor(); 
    $inner = $this->getMock('IRelationManager');
    $factory = $this->getMock('IRelationManagerFactory');
    $factory
      ->expects($this->once())
      ->method('from_config_name_and_descriptor')
      ->with($this->equalTo($inner_name),$this->equalTo($descriptor))
      ->will($this->returnValue($inner));
    $cache = $this->getMock('IPrefetchingCache');
    $cache_factory = $this->getMock('ICacheFactory');
    $cache_factory
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo($cache_name))
      ->will($this->returnValue($cache));
    $versioning = $this->getMock('ICacheVersioning');
    $cache_versioning_factory = $this->getMock('ICacheVersioningFactory');
    $cache_versioning_factory
      ->expects($this->once())
      ->method('from_cache_prefix_and_descriptor')
      ->with($this->equalTo($cache),$this->equalTo($prefix),$this->equalTo($descriptor))
      ->will($this->returnValue($versioning));
    $mockery = array(
      'get_cache_factory' => $cache_factory,
      'get_cache_versioning_factory' => $cache_versioning_factory,
    );
    $framework = $this->getMock('Framework',array_keys($mockery));
    $this->setMockery($framework,$mockery);
    $this->set_global_mock('Framework',$framework);
    $config = array(
      'inner'=>$inner_name,
      'cache'=>$cache_name,
      'prefix'=>$prefix,
    );

    $rm = $crmf->from_config_and_descriptor($factory,$config,$descriptor);
    $this->assertInstanceOf('IRelationManager',$rm);
  }
  public function testConfigWithVersioningName(){
    $crmf = $this->getSUT();
    $prefix = 'x';
    $inner_name = 'inner-name';
    $cache_name = 'cache-name';
    $versioning_name = 'versioning-name';
    $descriptor = $this->getUserlikeFieldsDescriptor(); 
    $inner = $this->getMock('IRelationManager');
    $factory = $this->getMock('IRelationManagerFactory');
    $factory
      ->expects($this->once())
      ->method('from_config_name_and_descriptor')
      ->with($this->equalTo($inner_name),$this->equalTo($descriptor))
      ->will($this->returnValue($inner));
    $cache = $this->getMock('IPrefetchingCache');
    $cache_factory = $this->getMock('ICacheFactory');
    $cache_factory
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo($cache_name))
      ->will($this->returnValue($cache));
    $versioning = $this->getMock('ICacheVersioning');
    $cache_versioning_factory = $this->getMock('ICacheVersioningFactory');
    $cache_versioning_factory
      ->expects($this->once())
      ->method('from_config_name')
      ->with($this->equalTo($versioning_name))
      ->will($this->returnValue($versioning));
    $mockery = array(
      'get_cache_factory' => $cache_factory,
      'get_cache_versioning_factory' => $cache_versioning_factory,
    );
    $framework = $this->getMock('Framework',array_keys($mockery));
    $this->setMockery($framework,$mockery);
    $this->set_global_mock('Framework',$framework);
    $config = array(
      'inner'=>$inner_name,
      'cache'=>$cache_name,
      'prefix'=>$prefix,
      'versioning'=>$versioning_name,
    );

    $rm = $crmf->from_config_and_descriptor($factory,$config,$descriptor);
    $this->assertInstanceOf('IRelationManager',$rm);
  }
}
?>
