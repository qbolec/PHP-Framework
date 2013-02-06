<?php
class HandlerTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException HTTPNotFoundException
   */
  public function testDoesNotGoFurther()
  {
    $h = $this->getMockForAbstractClass('Handler');
    $r = $this->getMock('IRequest');
    $e = new RequestEnv($r);
    $h->resolve_path(array('a'),$e);
  }
  public function testDoesPointToItself()
  {
    $h = $this->getMockForAbstractClass('Handler');
    $r = $this->getMock('IRequest');
    $e = new RequestEnv($r);
    $resolution = $h->resolve_path(array(),$e);
    $this->assertInstanceOf('IResolution',$resolution);
    $this->assertEquals($h,$resolution->get_handler());
    $this->assertEquals($e,$resolution->get_env());
  }
}
?>
