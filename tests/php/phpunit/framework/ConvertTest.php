<?php
class ConvertTest extends PHPUnit_Framework_TestCase
{
  
  /**
   * @dataProvider integers
   */
  public function testToInt($x){
    $y=Convert::to_int($x);
    $this->assertTrue($x==$y);
    $this->assertInternalType('int',$y);
  }
  /**
   * @dataProvider nonIntegers
   * @expectedException CouldNotConvertException
   */
  public function testBadStringToInt($x){
    Convert::to_int($x);
  }
  public function integers(){
    return array(
      array(41),
      array("42"),
      array(43.0),
      array(true),
      array(false),
    );
  }
  public function nonIntegers(){
    return array(
      array("42.0"),
      array(42.5),
      array(null),
      array(""),
      array(array()),
      array("1e1"),
      array("kalafior"),
      array(" 0"),
      array("+1"),
    );
  }
  /**
   * @dataProvider toHtml
   */
  public function testToHtml($text,$html){
    $this->assertSame($html,Convert::to_html($text));
  }
  public function toHtml(){
    return array(
      array('',''),
      array("\n","\n"),
      array("&","&amp;"),
      array("<","&lt;"),
      array('"',"&quot;"),
      array('zażółć gęślą&jaźń','zażółć gęślą&amp;jaźń'),
    );
  }
}
?>
