<?php

class HTTPBadRequestExceptionTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor(){
    
    $request_env = $this->getMock('IRequestEnv');
    $exception = $this->getMock('SimpleValidationException');

    $explanation = 'whatever';

    $explainer = $this->getMock('IValidationExceptionExplainer');
    $explainer
      ->expects($this->once())
      ->method('explain')
      ->with($this->equalTo($exception))
      ->will($this->returnValue($explanation));



    $e = new HTTPBadRequestException($explainer,$exception,$request_env);
    $this->assertInstanceOf('HTTPException',$e);
    $this->assertEquals(400,$e->getCode());
    $this->assertEquals('Bad Request',$e->getMessage());
    $this->assertEquals($exception,$e->getPrevious());
    $response = ResponseFactory::get_instance()->from_http_exception($e);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->stringContains($explanation));

    $response->send($output);
  }
}
?>
