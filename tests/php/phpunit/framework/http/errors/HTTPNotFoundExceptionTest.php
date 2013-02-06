<?php

class HTTPNotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
  public function testConstructor(){
    $r = $this->getMock('IRequestEnv');
    $e = new HTTPNotFoundException($r);
    $this->assertInstanceOf('HTTPException',$e);
    $this->assertEquals(404,$e->getCode());
    $this->assertEquals('Not Found',$e->getMessage());
  }
}
?>
