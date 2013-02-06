<?php
class SimpleCachePersistenceManagerFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new SimpleCachePersistenceManagerFactory();
  }
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IConfigurablePersistenceManagerFactory',$f);
  }
  public function testFromConfigAndDescriptor(){
    $f = $this->getSUT();
    $ff = $this->getMock('IPersistenceManagerFactory');
    $cache_name = 'cache-name';
    $key_prefix = 'key-prefix';
    $config = array(
      'cache' => $cache_name,
      'key_prefix' => $key_prefix,
    );

    $cache = $this->getMock('IPrefetchingCache');

    $cache_factory = $this->getMock('ICacheFactory');
    $cache_factory
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo($cache_name))
      ->will($this->returnValue($cache));

    $framework = $this->getMock('Framework',array('get_cache_factory'));
    $this->setMockery($framework,array('get_cache_factory'=>$cache_factory));
    $this->set_global_mock('Framework',$framework);
    
    $descriptor = $this->getMock('IFieldsDescriptor');
    $pm = $f->from_config_and_descriptor($ff, $config, $descriptor);
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
}
?>
