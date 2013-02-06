<?php
class ArrayValidatorTest extends FrameworkTestCase
{
  public function testInterface(){
    $m = $this->getMock('IValidator');
    $v = new ArrayValidator($m);
    $this->assertInstanceOf('IValidator',$v);
  }
  public function testRejectsNonArray(){
    $m = $this->getMock('IValidator');
    $v = new ArrayValidator($m);
    $this->assertSame(false,$v->is_valid(1)); 
  }
  public function testForcesNumbering(){
    $m = $this->getMock('IValidator');
    $m
      ->expects($this->once())
      ->method('get_error')
      ->will($this->returnValue(null));
    $v = new ArrayValidator($m);
    $this->assertInstanceOf('IValidationException',$v->get_error(array(1=>'ok')));
  }
  public function testCombinesErrors(){
    $ex = $this->getMock('IValidationException');
    $m = $this->getMock('IValidator');
    $m
      ->expects($this->once())
      ->method('get_error')
      ->with('bad')
      ->will($this->returnValue($ex));
    $v = new ArrayValidator($m);
    $this->assertInstanceOf('MultiValidationException',$v->get_error(array(1=>'bad')));
  }
  public function testCombinesFieldErrors(){
    $ex = $this->getMock('IValidationException');
    $m = $this->getMock('IValidator');
    $m
      ->expects($this->exactly(2))
      ->method('get_error')
      ->with('bad')
      ->will($this->returnValue($ex));
    $v = new ArrayValidator($m);
    $this->assertInstanceOf('StructureValidationException',$v->get_error(array(0=>'bad',1=>'bad')));
  }
  public function testAcceptsSomething(){
    $ex = $this->getMock('IValidationException');
    $m = $this->getMock('IValidator');
    $m
      ->expects($this->exactly(2))
      ->method('get_error')
      ->with('ok')
      ->will($this->returnValue(null));
    $v = new ArrayValidator($m);
    $this->assertSame(null,$v->get_error(array(0=>'ok',1=>'ok')));
   }
}
?>
