<?php
class SimplePDOPersistenceManagerFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new SimplePDOPersistenceManagerFactory();
  }
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IConfigurablePersistenceManagerFactory',$f);
  }
  public function testFromConfigAndDescriptor(){
    $f = $this->getSUT();
    $ff = $this->getMock('IPersistenceManagerFactory');
    $pdo_name = 'pdo-name';
    $table_name = 'table-name';
    $sharding_name = 'sharding-name';
    $config = array(
      'pdo' => $pdo_name,
      'table' => $table_name,
      'sharding' => $sharding_name, 
    );

    $sharding = $this->getMock('ISharding');

    $sharding_factory = $this->getMock('IShardingFactory');
    $sharding_factory
      ->expects($this->once())
      ->method('from_config_name')
      ->with($this->equalTo($sharding_name))
      ->will($this->returnValue($sharding));

    $framework = $this->getMock('Framework',array('get_sharding_factory'));
    $this->setMockery($framework,array('get_sharding_factory'=>$sharding_factory));
    $this->set_global_mock('Framework',$framework);
    
    $descriptor = $this->getMock('IFieldsDescriptor');
    $pm = $f->from_config_and_descriptor($ff, $config, $descriptor);
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
}
?>
