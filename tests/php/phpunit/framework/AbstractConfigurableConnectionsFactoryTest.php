<?php
abstract class FakeConfigurableConnectionsFactory extends AbstractConfigurableConnectionsFactory
{
  public function go(){
    return $this->get_connection_for_config_path('a/b/c');
  }
}
class AbstractConfigurableConnectionsFactoryTest extends FrameworkTestCase
{
  public function testConfigurability(){
    $config = $this->getMock('IConfig');
    $config
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('a/b/c'))
      ->will($this->returnValue(array('akuku')));

    $this->set_global_mock('Config',$config);
    
    $a = $this->getMockForAbstractClass('FakeConfigurableConnectionsFactory');
    $a
      ->expects($this->once())
      ->method('spawn')
      ->with($this->equalTo(array('akuku')))
      ->will($this->returnValue(42));
    $this->assertSame(42,$a->go());
    $this->assertSame(42,$a->go());
  }
}
?>
