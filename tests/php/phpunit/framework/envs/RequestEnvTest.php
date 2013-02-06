<?php
class RequestEnvTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $request = $this->getMock('IRequest');
    $this->assertInstanceOf('IRequestEnv',new RequestEnv($request));
  }
}
?>
