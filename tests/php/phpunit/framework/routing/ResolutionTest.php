<?php
class ResolutionTest extends PHPUnit_Framework_TestCase
{
  public function testFromHandlerAndEnv(){
    $h = $this->getMock('IHandler');
    $e = $this->getMock('IRequestEnv');
    $r = new Resolution($h,$e);
    $this->assertInstanceOf('IResolution',$r);
    $this->assertEquals($h,$r->get_handler());
    $this->assertEquals($e,$r->get_env());
  }
}
?>
