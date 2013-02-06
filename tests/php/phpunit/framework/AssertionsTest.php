<?php
class AssertionsTest extends FrameworkTestCase
{
  public function testInterface(){
    $this->assertInstanceOf('IAssertions',Assertions::get_instance());
  }
  /**
   * @expectedException LogicException
   */
  public function testHaltsIf(){
    Assertions::get_instance()->halt_if(true);
  }
  /**
   * @expectedException LogicException
   */
  public function testHaltsUnless(){
    Assertions::get_instance()->halt_unless(false);
  }
  public function testDoesNotHalt(){
    Assertions::get_instance()->halt_if(false);
    Assertions::get_instance()->halt_unless(true);
  }
  public function testHaltsIf2(){
    $a = $this->getMock('Assertions',array('halt'));
    $a->expects($this->once())
      ->method('halt');
    $a->halt_if(true);
  }
  public function testHaltsUnless2(){
    $a = $this->getMock('Assertions',array('halt'));
    $a->expects($this->once())
      ->method('halt');
    $a->halt_unless(false);
  }
  public function testWarnLogs(){
    $logger = $this->getMock('ILogger');
    $logger
      ->expects($this->once())
      ->method('log');
    
    $framework = $this->getMock('Framework',array('get_logger'));
    $framework
      ->expects($this->once())
      ->method('get_logger')
      ->will($this->returnValue($logger));

    $this->set_global_mock('Framework',$framework);

    Assertions::get_instance()->warn_if(true);
  }
  public function testWarnsIf(){
    $a = $this->getMock('Assertions',array('warn'));
    $a->expects($this->once())
      ->method('warn');
    $a->warn_if(true);
  }
  public function testWarnsUnless(){
    $a = $this->getMock('Assertions',array('warn'));
    $a->expects($this->once())
      ->method('warn');
    $a->warn_unless(false);
  }
  public function testDoesNotWarn(){
    $a = $this->getMock('Assertions',array('warn'));
    $a->expects($this->never())
      ->method('warn');
    $a->warn_if(false);
    $a->warn_unless(true);
  }



}
?>
