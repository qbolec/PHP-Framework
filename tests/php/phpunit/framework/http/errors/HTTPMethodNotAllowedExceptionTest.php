<?php

class HTTPMethodNotAllowedExceptionTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor(){
    $request_env = $this->getMock('IRequestEnv');

    $e = new HTTPMethodNotAllowedException(array(),$request_env);
    $this->assertInstanceOf('HTTPException',$e);
    $this->assertEquals(405,$e->getCode());
    $this->assertEquals('Method Not Allowed',$e->getMessage());
  }
  /**
   * @dataProvider headers
   */
  public function testHeaders(array $allowed,array $expected){
    $request_env = $this->getMock('IRequestEnv');
    $e = new HTTPMethodNotAllowedException($allowed,$request_env);
 
    $response = ResponseFactory::get_instance()->from_http_exception($e);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->any())
      ->method('send_header')
      ->will($this->returnCallback(function($key,$value)use(&$expected){
        if(array_key_exists($key,$expected)&& $expected[$key]==$value){
          unset($expected[$key]);
        }
      }));

    $response->send($output);
    $this->assertSame(array(),$expected);
  }
  public function headers(){
    return array(
      array(array('POST'),array('Allow' =>'POST')),
      array(array('POST','GET'),array('Allow'=>'POST, GET')),
      array(array(),array('Allow'=>'')),
    );
  }
}
?>
