<?php
abstract class JSONHandlerPostImp extends JSONHandler implements IPostJSONHandler
{
}
abstract class JSONHandlerGetImp extends JSONHandler implements IGetJSONHandler
{
}
abstract class JSONHandlerDeleteImp extends JSONHandler implements IDeleteJSONHandler
{
}
class JSONHandlerTest extends FrameworkTestCase
{
  public function testInterface(){
    $m = new MethodicHandler();
    $this->assertInstanceOf('IHandler',$m);
  }
  private function getEnv(IRequest $request){
    return new ApplicationEnv($request);
  }
  /**
   * @expectedException HTTPMethodNotAllowedException
   * @dataProvider method
   */
  public function testInvalidMethodsNotAllowed($method){
    $m = new JSONHandler();
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue($method));
    $env = $this->getEnv($request);
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
    $result = array(42);
    $input = array(13);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_GET));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_uri_param')
      ->with($this->equalTo('data'))
      ->will($this->returnValue(json_encode($input)));
    $env = $this->getEnv($request);
    $response = $this->getMock('IResponse');
    $m = $this->getMockForAbstractClass('JSONHandlerGetImp');
    $m
      ->expects($this->once())
      ->method('get_get_data')
      ->will($this->returnValue($result));
    $m
      ->expects($this->once())
      ->method('get_get_validator')
      ->will($this->returnValue($validator));

    $response = $m->handle($env);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo(json_encode($result)));

    $response->send($output);
  }
  public function testPost(){
    $result = array(42);
    $input = array(13);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_POST));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_post_value')
      ->with($this->equalTo('data'))
      ->will($this->returnValue(json_encode($input)));
    $env = $this->getEnv($request);
    $response = $this->getMock('IResponse');
    $m = $this->getMockForAbstractClass('JSONHandlerPostImp');
    $m
      ->expects($this->once())
      ->method('get_post_data')
      ->will($this->returnValue($result));
    $m
      ->expects($this->once())
      ->method('get_post_validator')
      ->will($this->returnValue($validator));

    $response = $m->handle($env);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo(json_encode($result)));

    $response->send($output);
  }
  public function testDelete(){
    $result = array(42);
    $input = array(13);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_DELETE));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_uri_param')
      ->with($this->equalTo('data'))
      ->will($this->returnValue(json_encode($input)));
    $env = $this->getEnv($request);
    $m = $this->getMockForAbstractClass('JSONHandlerDeleteImp');
    $m
      ->expects($this->once())
      ->method('get_delete_data')
      ->will($this->returnValue($result));
    $m
      ->expects($this->once())
      ->method('get_delete_validator')
      ->will($this->returnValue($validator));

    $response = $m->handle($env);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo(json_encode($result)));

    $response->send($output);
  }
  /**
   * @expectedException HTTPBadRequestException
   * @dataProvider invalidData
   */
  public function testPostInvalidData($input){
    $result = array(42);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_POST));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_post_value')
      ->with($this->equalTo('data'))
      ->will($this->returnValue($input));
    $env = $this->getEnv($request);
    $m = $this->getMockForAbstractClass('JSONHandlerPostImp');
    $m
      ->expects($this->never())
      ->method('get_post_data');
    $m
      ->expects($this->any())
      ->method('get_post_validator')
      ->will($this->returnValue($validator));

    $m->handle($env);
  }
  /**
   * @expectedException HTTPBadRequestException
   * @dataProvider invalidData
   */
  public function testInvalidData($input){
    $result = array(42);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->once())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_GET));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_uri_param')
      ->with($this->equalTo('data'))
      ->will($this->returnValue($input));
    $env = $this->getEnv($request);
    $response = $this->getMock('IResponse');
    $m = $this->getMockForAbstractClass('JSONHandlerGetImp');
    $m
      ->expects($this->never())
      ->method('get_get_data');
    $m
      ->expects($this->any())
      ->method('get_get_validator')
      ->will($this->returnValue($validator));
    
    $m->handle($env);
  }
  /**
   * @expectedException HTTPBadRequestException
   * @dataProvider invalidData
   */
  public function testDeleteInvalidData($input){
    $result = array(42);
    $validator = new ArrayValidator(new IntValidator());
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_method')
      ->will($this->returnValue(IRequest::METHOD_DELETE));
    $request
      ->expects($this->atLeastOnce())
      ->method('get_uri_param')
      ->with($this->equalTo('data'))
      ->will($this->returnValue($input));
    $env = $this->getEnv($request);
    $m = $this->getMockForAbstractClass('JSONHandlerDeleteImp');
    $m
      ->expects($this->never())
      ->method('get_delete_data');
    $m
      ->expects($this->any())
      ->method('get_delete_validator')
      ->will($this->returnValue($validator));
    
    $m->handle($env);
  }
  public function invalidData(){
    return array(
      array(null),
      array(''),
      array(array(1,2)),
      array('kalafior'),
      array('[1,'),
      array('1'),
      array('1,2'),
      array('[[],[]]'),
      array('[[1]]'),
      array(1),
    );
  } 
}
?>
