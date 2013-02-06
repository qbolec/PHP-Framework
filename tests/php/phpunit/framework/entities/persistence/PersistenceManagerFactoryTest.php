<?php
class PersistenceManagerFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IPersistenceManagerFactory',$f);
    $this->assertInstanceOf('IGetInstance',$f);
    $fd = $this->getUserlikeFieldsDescriptor();
    $sharding = $this->getMock('ISharding');
    $cache = $this->getMock('IPrefetchingCache');
    for($i=0;$i<2;++$i){
      $p[$i] = $this->getMock('IPersistenceManager');
      $p[$i]
        ->expects($this->any())
        ->method('get_fields_descriptor')
        ->will($this->returnValue($fd));
    }
    $this->assertInstanceOf('IPrefetchingPersistenceManager',$f->get_merged_with_cache($p[0],$p[1],$cache,'x'));
  }
  private function getSUT(){
    return new PersistenceManagerFactory();
  }
  public function testPDOFromConfig(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(
        array(
          'shardings' => array(
            'none' => array(
              'type' => 'none',
              'config' => null,
            ),
          ),
          'entities' => array(
            'base_question' => array(
              'type' => 'pdo',
              'config' => array(
                'pdo' => 'questions',
                'table' => 'question',
                'sharding' => 'none',
              ),
            ),
          ),
          'pdos' => array(
            'masters' => array(
              'questions' => array(
                0 => $this->get_test_pdo_config(),
              ),
            ),
          ),
        )
      ));
    $this->set_global_mock('Config',$config);
    $pmf = $this->getSUT();
    $fd = $this->getMock('IFieldsDescriptor');
    $pm = $pmf->from_config_name_and_descriptor('base_question',$fd);
    $this->assertInstanceOf('IPersistenceManager',$pm);
    $this->assertInstanceOf('IPrefetchingPersistenceManager',$pm);
  }
  public function testLayeredFromConfig(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(
        array(
          'shardings' => array(
            'none' => array(
              'type' => 'none',
              'config' => null,
            ),
          ),
          'entities' => array(
            'AandB' => array(
              'type' => 'layered',
              'config' => array(
                'near' => 'A',
                'far' => 'B',
              ),
            ),
            'A' => array(
              'type' => 'cache',
              'config' => array(
                'cache' => 'a_cache',
                'key_prefix' => 'pref',
              ),
            ),
            'B' => array(
              'type' => 'pdo',
              'config' => array(
                'pdo' => 'questions',
                'table' => 'question',
                'sharding' => 'none',
              ),
            ),
          ),
          'pdos' => array(
            'masters' => array(
              'questions' => array(
                0 => $this->get_test_pdo_config(),
              ),
            ),
          ),
          'caches' => array(
            'a_cache' => array(
              'type' => 'array',
              'config' => null,
            ),
          ),
        )
      ));
    $this->set_global_mock('Config',$config);
    $pmf = $this->getSUT();
    $fd = $this->getMock('IFieldsDescriptor');
    $pm = $pmf->from_config_name_and_descriptor('AandB',$fd);
    $this->assertInstanceOf('IPersistenceManager',$pm);
    $this->assertInstanceOf('IPrefetchingPersistenceManager',$pm);
  }
  public function testVersionedFromConfig(){
    $config = $this->getMockForAbstractClass('AbstractConfig');
    $config
      ->expects($this->once())
      ->method('get_tree')
      ->will($this->returnValue(
        array(
          'shardings' => array(
            'none' => array(
              'type' => 'none',
              'config' => null,
            ),
          ),
          'versionings' => array(
            'versioning-name' => array(
              'cache' => 'cache-name',
              'prefix' => 'whocares',
              'columns' => array('id'),
            ),
          ),
          'entities' => array(
            'base_question' => array(
              'type' => 'versioned',
              'config' => array(
                'inner' => 'test2',
                'versioning' => 'versioning-name',
              ),
            ),
            'test2'=> array(
              'type' => 'pdo',
              'config' => array(
                'pdo' => 'questions',
                'table' => 'question',
                'sharding' => 'none',
              ),
            ),
          ),
          'pdos' => array(
            'masters' => array(
              'questions' => array(
                0 => $this->get_test_pdo_config(),
              ),
            ),
          ),
          'caches' => array(
            'cache-name' => array(
              'type' => 'array',
              'config' => null,
            ),
          ),
        )
      ));
    $this->set_global_mock('Config',$config);
    $pmf = $this->getSUT();
    $fd = $this->getMock('IFieldsDescriptor');
    $pm = $pmf->from_config_name_and_descriptor('base_question',$fd);
    $this->assertInstanceOf('IPersistenceManager',$pm);
    $this->assertInstanceOf('IPrefetchingPersistenceManager',$pm);
  }
}
?>
