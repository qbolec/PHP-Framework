<?php
class HTTPBadJSONRequestExceptionTest extends FrameworkTestCase
{
  public function testConstructor(){
    
    $request_env = $this->getMock('IRequestEnv');
    $exception = $this->getMock('SimpleValidationException');

    $explanation = '{}';

    $explainer = $this->getMock('IValidationExceptionExplainer');
    $explainer
      ->expects($this->once())
      ->method('explain')
      ->with($this->equalTo($exception))
      ->will($this->returnValue($explanation));

    $factory = $this->getMock('IValidationExceptionExplainerFactory');
    $factory
      ->expects($this->once())
      ->method('get_json')
      ->will($this->returnValue($explainer));

    $framework = $this->getMock('Framework',array('get_validation_exception_explainer_factory'));
    $framework
      ->expects($this->once())
      ->method('get_validation_exception_explainer_factory')
      ->will($this->returnValue($factory));
    $this->set_global_mock('Framework',$framework);


    $e = new HTTPBadJSONRequestException($exception,$request_env);
    $this->assertInstanceOf('HTTPException',$e);
    $this->assertEquals(400,$e->getCode());
    $this->assertEquals('Bad Request',$e->getMessage());
    $this->assertEquals($exception,$e->getPrevious());
    $response = ResponseFactory::get_instance()->from_http_exception($e);

    $output = $this->getMock('IOutput');
    $output
      ->expects($this->once())
      ->method('send_body')
      ->with($this->equalTo($explanation));

    $response->send($output);
  }
}
?>
