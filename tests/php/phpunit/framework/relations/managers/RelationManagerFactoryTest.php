<?php
class RelationManagerFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new RelationManagerFactory();
  }
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IRelationManagerFactory',$f);
  }
  public function testFromConfigNameAndDescriptor(){
    $relation_manager = $this->getMock('IRelationManager');
    $relation_name='test_relation';
    $conf = array();
    $type = 'test_type';
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'relations' => array(
          $relation_name => array(
            'type' => $type,
            'config' => $conf,
          ),
        ),
      )));
    $this->set_global_mock('Config',$config);

    $descriptor = $this->getMock('IFieldsDescriptor');

    $f = $this->getMock('RelationManagerFactory',array('get_factory_by_type'));
    $factory = $this->getMock('IConfigurableRelationManagerFactory');
    $factory
      ->expects($this->once())
      ->method('from_config_and_descriptor')
      //@todo : zamienić na identicalTo, gdy uda się wywalić clonowanie z PHPUnit
      ->with($this->isInstanceOf('RelationManagerFactory'),$this->equalTo($conf),$this->equalTo($descriptor))
      ->will($this->returnValue($relation_manager));

    $f
      ->expects($this->once())
      ->method('get_factory_by_type')
      ->with($this->equalTo($type))
      ->will($this->returnValue($factory));

    $this->assertSame($relation_manager,$f->from_config_name_and_descriptor($relation_name,$descriptor));
  }
  public function testFromConfigNameAndDescriptorPDO(){
    $relation_name='test_relation';
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(array(
        'relations' => array(
          $relation_name => array(
            'type' => 'cached',
            'config' => array(
              'inner' => 'inner_rel',
              'cache' => 'test_cache',
              'prefix' => 'test_prefix',
            ),
          ),
          'inner_rel' => array(
            'type' => 'pdo',
            'config' => array(
              'pdo'=>'test_pdo',
              'table'=>'test_table',
              'sharding'=>'test_sharding',
            ),
          ),
        ),
      )));
    $this->set_global_mock('Config',$config);

    $sharding = $this->getMock('ISharding');
    $sharding_factory = $this->getMock('IShardingFactory');
    $sharding_factory
      ->expects($this->once())
      ->method('from_config_name')
      ->with($this->equalTo('test_sharding'))
      ->will($this->returnValue($sharding));
    $cache = $this->getMock('IPrefetchingCache');
    $cache_factory = $this->getMock('ICacheFactory');
    $cache_factory
      ->expects($this->once())
      ->method('get_cache')
      ->with($this->equalTo('test_cache'))
      ->will($this->returnValue($cache));
    $mockery = array(
      'get_sharding_factory' => $sharding_factory,
      'get_cache_factory' => $cache_factory,
    );
    $framework = $this->getMock('Framework',array_keys($mockery));
    foreach($mockery as $foo => $res){
      $framework
        ->expects($this->atLeastOnce())
        ->method($foo)
        ->will($this->returnValue($res));
    }

    $this->set_global_mock('Framework',$framework);

    $fields_descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));

    $f = $this->getSUT();
    $this->assertInstanceOf('IRelationManager',$f->from_config_name_and_descriptor($relation_name,$fields_descriptor));
  }
  public function testGetArray(){
    $fields_descriptor = FieldsDescriptorFactory::get_instance()->get_from_array(array(
      'a' => new IntFieldType(),
      'b' => new IntFieldType(),
    ));

    $f = $this->getSUT();
    $array = $f->get_array($fields_descriptor, array(), array());
    $this->assertInstanceOf('ArrayRelationManager', $array);
    $this->assertInstanceOf('AbstractRelationManager', $array);
  }

}
?>
