<?php
class CacheVersioningFactoryTest extends FrameworkTestCase
{
  public function getSUT(){
    return new CacheVersioningFactory();
  }
  public function testInterface(){
    $cvf = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$cvf);
    $this->assertInstanceOf('ICacheVersioningFactory',$cvf);
  }
  public function testFromConfigName(){
    $name = 'x';

    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'versionings' => array(
          $name => array(
            'cache' => 'cache-name',
            'prefix' => 'pref',
            'columns' => array('a','b'),
          ),
        ),
        'caches' => array(
          'cache-name' => array(
            'type' => 'array',
            'config' => null,
          ),
        ),
      )));
    $this->set_global_mock('Config',$config);

    $cvf = $this->getSUT();
    $cv = $cvf->from_config_name($name);
    $this->assertInstanceOf('ICacheVersioning',$cv);
  }
  public function testFromCachePrefixAndDescriptor(){
    $cache = $this->getMock('IPrefetchingCache');
    $prefix = 'x';
    $descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));
    
    $cvf = $this->getSUT();
    $cv = $cvf->from_cache_prefix_and_descriptor($cache,$prefix,$descriptor);
    $this->assertInstanceOf('ICacheVersioning',$cv);
  }
}
?>
