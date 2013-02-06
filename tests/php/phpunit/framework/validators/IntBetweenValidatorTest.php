<?php
class IntBetweenValidatorTest extends FrameworkTestCase
{
  private function get_sut($min,$max){
    return new IntBetweenValidator($min,$max);
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
      array(null,null,1),
      array(1,null,1),
      array(null,2,1),
      array(1,2,1),
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
      array(null,1,1),
      array(2,null,1),
      array(1,1,1),
      array(2,2,1),
      array(null,null,1.0),
      array(null,null,'1'),
      array(null,null,'-1'),
      array(null,null,1.2),
      array(null,null,'a'),
      array(null,null,null),
      array(null,null,true),
      array(null,null,false),
      array(null,null,new stdClass()),
      array(null,null,new Exception()),
      array(null,null,array()),
    );
  }
}
?>
