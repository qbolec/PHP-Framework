<?php
class IntValidatorTest extends PHPUnit_Framework_TestCase
{
  private function validator(){
    return new IntValidator();
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
      array(1),
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
      array(1.0),
      array('1'),
      array('-1'),
      array(1.2),
      array('a'),
      array(null),
      array(true),
      array(false),
      array(new stdClass()),
      array(new Exception()),
      array(array()),
    );
  }
}
?>
