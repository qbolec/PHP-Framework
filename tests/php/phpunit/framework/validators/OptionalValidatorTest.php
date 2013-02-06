<?php
class OptionalValidatorTest extends FrameworkTestCase
{
  private function getSUT($inner){
    return new OptionalValidator($inner);
  }

  public function testInterface(){
    $inner = $this->getMock('IValidator');
    $validator = $this->getSUT($inner);
    $this->assertInstanceOf('IValidator',$validator);
  }

  public function testNull(){
    $inner = $this->getMock('IValidator');
    $inner
      ->expects($this->never())
      ->method('get_error');
    $validator = $this->getSUT($inner);
    $this->assertSame(true,$validator->is_valid(null));
    $this->assertSame(null,$validator->get_error(null));
  }
  public function testOk(){
    $value = 42;
    $inner = $this->getMock('IValidator');
    $inner
      ->expects($this->atLeastOnce())
      ->method('get_error')
      ->with($value)
      ->will($this->returnValue(null));
    $validator = $this->getSUT($inner);
    $this->assertSame(null,$validator->get_error($value));
  }
  public function testBad(){
    $value = 13;
    $e = new CouldNotConvertException($value);
    $inner = $this->getMock('IValidator');
    $inner
      ->expects($this->atLeastOnce())
      ->method('get_error')
      ->with($value)
      ->will($this->returnValue($e));
    $validator = $this->getSUT($inner);
    $this->assertSame($e,$validator->get_error($value));
  }
}
?>
