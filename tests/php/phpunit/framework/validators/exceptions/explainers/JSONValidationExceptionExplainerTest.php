<?php
class JSONValidationExceptionExplainerTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = new JSONValidationExceptionExplainer();
    $this->assertInstanceOf('IValidationExceptionExplainer',$a);
  }
  public function testPaths(){
    $inner_ex = $this->getMock('IValidationException');
    $inner_ex
      ->expects($this->never())
      ->method('to_tree');
    $inner_ex
      ->expects($this->once())
      ->method('getMessage')
      ->will($this->returnValue('akuku'));

    $e = $this->getMock('IValidationException'); 
    $e
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array(
        'fields'=>array(
          'brick_road' => array(
            'errors'=>array($inner_ex),
          ),
        ),
      )));
    $a = new JSONValidationExceptionExplainer();
    $x = json_decode($a->explain($e),true);
    $this->assertSame('akuku',$x['fields']['brick_road']['errors'][0]['message']);
  }
}
?>
