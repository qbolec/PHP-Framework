<?php
class FloatValidatorTest extends FrameworkTestCase
{
  private function validator(){
    return new FloatValidator();
  }
  public function testInterface(){
    $s = $this->validator();
    $this->assertInstanceOf('IValidator',$s);
  }
  /**
   * @dataProvider goodData
   */
  public function testGood($input){
    $s = $this->validator();
    $this->assertTrue($s->is_valid($input));
    $this->assertNull($s->get_error($input));
  }
  public function goodData(){
    return array(
      array(1.0),
      array(1.01),
    );
  }
  /**
   * @dataProvider badData
   */
  public function testSelectiveness($bad){
    $s = $this->validator();
    $this->assertFalse($s->is_valid($bad));
  }
  public function badData(){
    return array(
      array(1),
      array('1'),
      array(true),
      array(0),
      array(''),
      array(null),
      array(new stdClass()),
      array(new Exception()),
      array(array()),
    );
  }
}
?>
