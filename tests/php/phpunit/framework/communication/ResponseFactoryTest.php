<?php
class ResponseFactoryTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $response_factory = new ResponseFactory();
    $this->assertInstanceOf('IResponseFactory',$response_factory);
    $this->assertInstanceOf('IGetInstance',$response_factory);
  }
  public function testFromHttpException(){
    $response_factory = new ResponseFactory();

    $response = $this->getMock('IResponse');
    $e = $this->getMock('IHTTPException');
    $e
      ->expects($this->once())
      ->method('get_response')
      ->with($this->equalTo($response_factory))
      ->will($this->returnValue($response));

    $this->assertSame($response,$response_factory->from_http_exception($e));
  }
  public function testFromHttpBody(){
    $body = '42';
    $r = ResponseFactory::get_instance()->from_http_body($body);
    $this->assertInstanceOf('IResponse',$r);
    $mock_output = $this->getMock('IOutput');
    $mock_output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo($body));
    $mock_output
      ->expects($this->never())
      ->method('send_header');
    $r->send($mock_output);
  }
  public function testJsonFromData(){
    $body = '[42]';
    $data = array(42);
    $r = ResponseFactory::get_instance()->json_from_data($data);
    $this->assertInstanceOf('IResponse',$r);
    $mock_output = $this->getMock('IOutput');
    $mock_output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo($body));
    $mock_output
      ->expects($this->atLeastOnce())
      ->method('send_header');
    $r->send($mock_output);
  }
}
?>
