<?php
class PCREValidatorTest extends FrameworkTestCase
{
  public function testInterface(){
    $s = $this->getSUT();
    $this->assertInstanceOf('IValidator',$s);
    $this->assertInstanceOf('INormalizer',$s);
    $this->assertInstanceOf('StringValidator',$s);
  }
  private function getSUT(){
    return new PCREValidator('/^[[:xdigit:]]{2}$/');
  }
  /**
   * @dataProvider goodData
   */
  public function testNormalization($input,$output){
    $s = $this->getSUT();
    $this->assertTrue($s->is_valid($input));
    $this->assertNull($s->get_error($input));
    $this->assertSame($output,$s->normalize($input));
  }
  public function goodData(){
    return array(
      array('ab','ab'),
      array('12','12'),
      array('a1','a1'),
    );
  }
  /**
   * @dataProvider badData
   */
  public function testSelectiveness($bad){
    $s = new PCREValidator('/^[[:xdigit:]]{2}$/');
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
      array(12),
      array('bad'),
      array('FU'),
      array(1.2),
    );
  }
}
?>
