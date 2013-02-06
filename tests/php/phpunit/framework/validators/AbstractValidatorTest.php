<?php
abstract class FakeAbstractValidator extends AbstractValidator
{
  public function compact_errors(array $errors){
    return parent::compact_errors($errors);
  }
}
class AbstractValidatorTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $a = $this->getMockForAbstractClass('AbstractValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->will($this->returnValue(null));
    $this->assertInstanceOf('IValidator',$a);
    $this->assertSame(true,$a->is_valid(42));
  }
  public function testMatch(){
    $a = $this->getMockForAbstractClass('AbstractValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->will($this->returnValue(null));
    $a->must_match(42);
  }
  /**
   * @expectedException CouldNotConvertException
   */
  public function testSelectiveness(){
    $a = $this->getMockForAbstractClass('AbstractValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->will($this->returnValue(new CouldNotConvertException(42)));
    $a->must_match(42);
  }
  public function testCompactErrors(){
    $a = $this->getMockForAbstractClass('FakeAbstractValidator');
    $this->assertSame(null,$a->compact_errors(array()));
    $ex = $this->getMock('IValidationException');
    $this->assertSame($ex,$a->compact_errors(array($ex)));
    $this->assertInstanceOf('MultiValidationException',$a->compact_errors(array($ex,$ex)));
  }
}
?>
