<?php
class HTTPUnauthorizedExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $r = $this->getMock('IRequestEnv');
    $e = new HTTPUnauthorizedException($r);
    $this->assertInstanceOf('HTTPException',$e);
    $this->assertEquals(401,$e->getCode());
    $this->assertEquals('Unauthorized',$e->getMessage());
  }
}
?>
