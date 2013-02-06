<?php
class StringValidatorTest extends PHPUnit_Framework_TestCase
{
  public function testInterface(){
    $s = new StringValidator();
    $this->assertInstanceOf('IValidator',$s);
    $this->assertInstanceOf('INormalizer',$s);
  }
  /**
   * @dataProvider goodData
   */
  public function testNormalization($input,$output){
    $s = new StringValidator();
    $this->assertTrue($s->is_valid($input));
    $this->assertNull($s->get_error($input));
    $this->assertSame($output,$s->normalize($input));
  }
  public function goodData(){
    return array(
      array('ok','ok'),
      array('1','1'),
      array('1.2','1.2'),
    );
  }
  /**
   * @dataProvider badData
   */
  public function testSelectiveness($bad){
    $s = new StringValidator();
    $this->assertFalse($s->is_valid($bad));
  }
  public function badData(){
    return array(
      array(null),
      array(true),
      array(false),
      array(array()),
      array(new stdClass()),
      array(new Exception()),//cos co ma toString
      array(1),
      array(1.2),
    );
  }
}
?>
