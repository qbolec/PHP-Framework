<?php
class StructureValidationExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e1 = $this->getMock('IValidationException');
    $e2 = $this->getMock('IValidationException');
    $e = new StructureValidationException(array('a'=>$e1,'b'=>$e2));
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
  public function testToTree(){
    for($i=0;$i<2;++$i){
      $e[$i] = $this->getMock('IValidationException');
      $e[$i]
        ->expects($this->once())
        ->method('to_tree')
        ->will($this->returnValue(array('errors'=>array($e[$i]))));
    }
    $s = new StructureValidationException(array('a'=>$e[0],'b'=>$e[1]));
    $this->assertSame(array(
      'fields'=>array(
        'a'=>array(
          'errors'=>array($e[0]),
        ),
        'b'=>array(
          'errors'=>array($e[1]),
        ),
      ),
    ),$s->to_tree()); 
  }
}
?>
