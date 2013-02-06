<?php
class LayeredPersistenceManagerFactoryTest extends FrameworkTestCase
{
  private function getSUT(){
    return new LayeredPersistenceManagerFactory();
  }
  public function testInterface(){
    $f = $this->getSUT();
    $this->assertInstanceOf('IGetInstance',$f);
    $this->assertInstanceOf('IConfigurablePersistenceManagerFactory',$f);
  }
  public function testFromConfigAndDescriptor(){
    $f = $this->getSUT();
    $ff = $this->getMock('IPersistenceManagerFactory');
    $near_name = 'near-name';
    $far_name = 'far-name';
    $config = array(
      'near' => $near_name,
      'far' => $far_name,
    );

    $pm[$near_name] = $this->getMock('IPersistenceManager');
    $pm[$far_name] = $this->getMock('IPersistenceManager');

    $descriptor = $this->getMock('IFieldsDescriptor');
    $ff
      ->expects($this->atLeastOnce())
      ->method('from_config_name_and_descriptor')
      ->with($this->anything(),$this->equalTo($descriptor))
      ->will($this->returnCallback(function($name)use($pm){return $pm[$name];}));

    $pm = $f->from_config_and_descriptor($ff, $config, $descriptor);
    $this->assertInstanceOf('IPersistenceManager',$pm);
  }
}
?>
