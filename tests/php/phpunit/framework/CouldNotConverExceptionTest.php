<?php
class CouldNotConvertExceptionTest extends FrameworkTestCase
{
  /**
   * @dataProvider whatever
   */
  public function testConstructor($x){
    $e = new CouldNotConvertException($x);
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
    $e->getMessage();
  }
  public function whatever(){
    return array(
      array(42),
      array("42"),
      array(43.0),
      array(array(true)),
      array(true),
      array(null),
      array(""),
    );
  }
}
?>
