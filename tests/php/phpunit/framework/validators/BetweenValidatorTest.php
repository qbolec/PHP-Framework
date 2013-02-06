<?php
class BetweenValidatorTest extends FrameworkTestCase
{
  private function get_sut($min,$max){
    return new BetweenValidator($min,$max);
  }
  public function testInterface(){
    $s = $this->get_sut(null,null);
    $this->assertInstanceOf('IValidator',$s);
  }
  /**
   * @dataProvider goodData
   */
  public function testGood($min,$max,$input){
    $s = $this->get_sut($min,$max);
    $this->assertTrue($s->is_valid($input));
    $this->assertNull($s->get_error($input));
  }
  public function goodData(){
    return array(
      array(null,null,1.0),
      array(1,null,1.0),
      array(null,2,"1"),
      array(1,2,1.1),
    );
  }
  /**
   * @dataProvider badData
   */
  public function testSelectiveness($min,$max,$bad){
    $s = $this->get_sut($min,$max);
    $this->assertFalse($s->is_valid($bad));
  }
  public function badData(){
    return array(
      array(null,1,1.1),
      array(2,null,1.9),
      array(1,1,1.1),
      array(2,2,1.9),
    );
  }
}
?>
