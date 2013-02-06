<?php
class RecordValidatorTest extends FrameworkTestCase
{
  public function testInterface(){
    $v = new RecordValidator(array());
    $this->assertInstanceOf('IValidator',$v);
  }
  public function testHandlesNonArray(){
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->never())
      ->method('get_error');

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->never())
      ->method('get_error');
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertInstanceOf('CouldNotConvertException',$v->get_error('whatever'));
  }
  public function testPassesDataCorrectly(){
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_a'))
      ->will($this->returnValue(null));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_b'))
      ->will($this->returnValue(null));
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertSame(null,$v->get_error(array('key_a'=>'value_a','key_b'=>'value_b')));
  } 
  public function testChecksMisses(){
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_a'))
      ->will($this->returnValue(null));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->never())
      ->method('get_error');
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertInstanceOf('IsMissingException',$v->get_error(array('key_a'=>'value_a')));
  }
  public function testChecksUnexpected(){
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_a'))
      ->will($this->returnValue(null));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_b'))
      ->will($this->returnValue(null));
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertInstanceOf('UnexpectedMemberException',$v->get_error(array('key_a'=>'value_a','key_b'=>'value_b','key_bad'=>'bad')));
  } 
  public function testCompactsErrors(){
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->never())
      ->method('get_error');

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_b'))
      ->will($this->returnValue(null));
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertInstanceOf('MultiValidationException',$v->get_error(array('key_b'=>'value_b','key_bad'=>'bad')));
  }
  public function testCompactsFieldErrors(){
    $ex = $this->getMock('IValidationException');
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_a'))
      ->will($this->returnValue($ex));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value_b'))
      ->will($this->returnValue($ex));
    $validators = array(
      'key_a' => $a,
      'key_b' => $b,
    );
    $v = new RecordValidator($validators);
    $this->assertInstanceOf('StructureValidationException',$v->get_error(array('key_a'=>'value_a','key_b'=>'value_b')));
  }
 
}
?>
