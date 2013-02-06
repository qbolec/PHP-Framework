<?php
abstract class MethodicHandlerImp extends MethodicHandler implements IGetHandler
{
}
class MethodicHandlerTest extends FrameworkTestCase
{
  public function testInterface(){
    $m = new MethodicHandler();
    $this->assertInstanceOf('IHandler',$m);
  }
  /**
   * @expectedException HTTPMethodNotAllowedException
   * @dataProvider method
   */
  public function testInvalidMethodsNotAllowed($method){
    $m = new MethodicHandler();
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue($method));
    $env = $this->getMock('IRequestEnv');
    $env
      ->expects($this->atLeastOnce())
      ->method('get_request')
      ->will($this->returnValue($request));
    $m->handle($env);
  }
  public function method(){
    return array(
      array(IRequest::METHOD_OPTIONS),
      array(IRequest::METHOD_POST),
      array(IRequest::METHOD_PUT),
      array(IRequest::METHOD_DELETE),
      array(IRequest::METHOD_GET),
      array(IRequest::METHOD_HEAD),
      array(IRequest::METHOD_CONNECT),
      array(IRequest::METHOD_TRACE),
    );
  }
  public function testGet(){
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_GET));
    $env = $this->getMock('IRequestEnv');
    $env
      ->expects($this->atLeastOnce())
      ->method('get_request')
      ->will($this->returnValue($request));
    $response = $this->getMock('IResponse');
    $m = $this->getMockForAbstractClass('MethodicHandlerImp');
    $m
      ->expects($this->once())
      ->method('handle_get')
      ->with($this->equalTo($env))
      ->will($this->returnValue($response));
    $this->assertSame($response,$m->handle($env));
  }
}
?>
