<?php
class AndValidatorTest extends FrameworkTestCase
{
  private function get_sut(array $validators){
    return new AndValidator($validators);
  }
  public function testInterface(){
    $s = $this->get_sut(array());
    $this->assertInstanceOf('IValidator',$s);
  }
  public function testEmptyGood(){
    $s = $this->get_sut(array());
    $input = 123;
    $this->assertTrue($s->is_valid($input));
    $this->assertNull($s->get_error($input));
  }
  public function testSingle(){
    $data = 123;
    $v = $this->getMock('IValidator');
    $s = $this->get_sut(array($v));
    $v
      ->expects($this->any())
      ->method('get_error')
      ->with($this->equalTo($data))
      ->will($this->returnValue(null));

    $this->assertTrue($s->is_valid($data));
    $this->assertNull($s->get_error($data));
  }
  public function testSingleBad(){
    $data = 123;
    $e = $this->getMock('IValidationException');
    $v = $this->getMock('IValidator');
    $s = $this->get_sut(array($v));
    $v
      ->expects($this->any())
      ->method('get_error')
      ->with($this->equalTo($data))
      ->will($this->returnValue($e));

    $this->assertFalse($s->is_valid($data));
    $this->assertSame($e,$s->get_error($data));
  }
}
?>
