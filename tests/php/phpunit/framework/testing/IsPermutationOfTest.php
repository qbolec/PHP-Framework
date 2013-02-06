<?php
class IsPermutationOfTest extends FrameworkTestCase
{
  /**
   * @dataProvider perms
   */
  public function testPositive($a,$b){
    $this->assertIsPermutationOf(array(1,3,2),array(3,1,2));
  }
  public function perms(){
    $a = new stdClass();
    $b = new stdClass();
    $c = new stdClass();
    return array(
      array(array("1","3","11"),array("3","11","1")),
      array(array(1,3,11),array(3,11,1)),
      array(array(),array()),
      array(array($a,$b,$c),array($c,$b,$a)),
    );
  }
  /**
   * @dataProvider notArray
   * @expectedException Exception
   */
  public function testNegativeNonArray($a,$b){
    $this->assertThat($a,$this->logicalNot($this->isPermutationOf($b)));
  }
  public function notArray(){
    return array(
      array(array(),new stdClass()),
      array(new stdClass,array()),
    );
  }
  /**
   * @dataProvider incorrectTypes
   */
  public function testIncorrectTypes($a,$b){
    $this->assertThat($a,$this->logicalNot($this->isPermutationOf($b)));
  }
  public function incorrectTypes(){
    $a = new stdClass();
    $b = new stdClass();
    $c = new stdClass();
    return array(
      array(array("1","3","11"),array("3","11",1)),
      array(array(1,3,11),array(3,11,true)),
      array(array(new stdClass()),array(array())),
      array(array($a,$b,$c),array($c,array(),$a)),
    );
  }
  /**
   * @dataProvider notAPerm
   */
  public function testNotAPerm($a,$b){
    $this->assertThat($a,$this->logicalNot($this->isPermutationOf($b)));
  }
  public function notAPerm(){
    $a = new stdClass();
    $b = new stdClass();
    $c = new stdClass();
    return array(
      array(array("1","3","11"),array("3","12",1)),
      array(array(1,3,11),array(3,11)),
      array(array(1,3,11,11),array(3,11,3,1)),
      array(array(1,3,11),array(3,11,1,1)),
      array(array(1,3,11),array(3,11,1,2)),
      array(array(),array(array())),
      array(array($a,$b,$c),array($c,$a,$a)),
      array(array($a,$b,$c),array($c,$b,$b,$a)),
    );
  }
  public function testToString(){
    $x = $this->isPermutationOf(array(1,2,3));
    $this->assertContains('1',$x->toString());
    $this->assertContains('2',$x->toString());
    $this->assertContains('3',$x->toString());
  }  
}
?>
