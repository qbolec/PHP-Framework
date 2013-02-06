<?php
class SimpleValidatorTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $s = $this->getMockForAbstractClass('SimpleValidator');
    $this->assertInstanceOf('IValidator',$s);
    $this->assertInstanceOf('INormalizer',$s);
    $s
      ->expects($this->once())
      ->method('normalize')
      ->will($this->returnValue(42));
    $this->assertTrue($s->is_valid("whatever"));
  }
  public function testGood(){
    $s = $this->getMockForAbstractClass('SimpleValidator');
    $e = new CouldNotConvertException("whatever");
    $s
      ->expects($this->once())
      ->method('normalize')
      ->will($this->throwException($e));
    $this->assertSame($e,$s->get_error("whatever"));
  }
  public function testBad(){
    $s = $this->getMockForAbstractClass('SimpleValidator');
    $s
      ->expects($this->once())
      ->method('normalize')
      ->will($this->throwException(new CouldNotConvertException("whatever")));
    $this->assertFalse($s->is_valid("whatever"));
  }
  

}
?>
