<?php
class AbstractApplicationTest extends FrameworkTestCase
{
  public function testInterface(){
    $application = $this->getMockForAbstractClass('AbstractApplication');
    $this->assertInstanceOf('IApplication',$application);
  }
  public function testBuildRequestFromGlobals()
  {
    $mock_output = $this->getMock('IOutput');
    $mock_output
      ->expects($this->once())
      ->method('send_status')
      ->with($this->equalTo(404),$this->equalTo('Not Found'));
    
    $mock_request_factory = $this->getMock('RequestFactory',array('from_globals'));
    $mock_request_factory
      ->expects($this->once())
      ->method('from_globals')
      ->will($this->returnValue(RequestFactory::get_instance()->from_globals()));

    $mock_logger = $this->getMock('ILogger');

    $mockery = array(
      'get_output'=>$mock_output,
      'get_request_factory'=>$mock_request_factory,
      'get_logger'=>$mock_logger,
    );

    $framework = $this->getMock('Framework',array_keys($mockery));
    foreach($mockery as $method_name => $fixed_value){
      $framework
        ->expects($this->any())
        ->method($method_name)
        ->will($this->returnValue($fixed_value));
    }

    $this->set_global_mock('Framework',$framework);

    $application = $this->getMockForAbstractClass('AbstractApplication');
    $application
      ->expects($this->any())
      ->method('get_root_router')
      ->will($this->returnValue(new EmptyRouter()));
    
    $application->run();

  }
}
?>
