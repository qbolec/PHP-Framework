<?php
class MapValidatorTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = $this->getMock('IValidator');
    $b = $this->getMock('IValidator');
    $v = new MapValidator($a,$b);
    $this->assertInstanceOf('IValidator',$v);
  }
  public function testForcesArray(){
    $a = $this->getMock('IValidator');
    $b = $this->getMock('IValidator');
    $v = new MapValidator($a,$b);
    $this->assertInstanceOf('IValidationException',$v->get_error('whatever'));
    $this->assertSame(null,$v->get_error(array()));
  }
  public function testValidatesKey(){
    $ex = $this->getMock('IValidationException');
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('key'))
      ->will($this->returnValue($ex));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value'))
      ->will($this->returnValue(null));
    $v = new MapValidator($a,$b);

    $this->assertInstanceOf('StructureValidationException',$v->get_error(array('key'=>'value')));
  }
  public function testValidatesValue(){
    $ex = $this->getMock('IValidationException');
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(null));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value'))
      ->will($this->returnValue($ex));
    $v = new MapValidator($a,$b);

    $this->assertInstanceOf('StructureValidationException',$v->get_error(array('key'=>'value')));
  }
  public function testAgregatesFieldErrors(){
    $ex = $this->getMock('IValidationException');
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('key'))
      ->will($this->returnValue($ex));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->once())
      ->method('get_error')
      ->with($this->equalTo('value'))
      ->will($this->returnValue($ex));
    $v = new MapValidator($a,$b);

    $this->assertInstanceOf('StructureValidationException',$v->get_error(array('key'=>'value')));
  }
  public function testAgregatesAllErrors(){
    $ex = $this->getMock('IValidationException');
    $a = $this->getMock('IValidator');
    $a
      ->expects($this->any())
      ->method('get_error')
      ->will($this->returnValue($ex));

    $b = $this->getMock('IValidator');
    $b
      ->expects($this->any())
      ->method('get_error')
      ->will($this->returnValue($ex));
    $v = new MapValidator($a,$b);

    $this->assertInstanceOf('StructureValidationException',$v->get_error(array('key'=>'value','key2'=>'value2')));
  }
}
?>
