<?php
class PartialResolutionTest extends PHPUnit_Framework_TestCase
{
  public function testFromHandlerAndEnv(){
    $r = $this->getMock('IPathResolver');
    $e = $this->getMock('IRequestEnv');
    $p = new PartialResolution($r,$e);
    $this->assertInstanceOf('IPartialResolution',$p);
    $this->assertEquals($r,$p->get_resolver());
    $this->assertEquals($e,$p->get_env());
  }

}
?>
