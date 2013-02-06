<?php
class MultiValidationExceptionTest extends FrameworkTestCase
{
  public function testInterface(){
    $e1 = $this->getMock('IValidationException');
    $e2 = $this->getMock('IValidationException');
    $e = new MultiValidationException(array($e1,$e2));
    $this->assertInstanceOf('IValidationException',$e);
    $this->assertInstanceOf('Exception',$e);
  }
  public function testToTreeJustErrors(){
    $e1 = $this->getMock('IValidationException');
    $e1
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array('errors'=>array($e1))));
    $e2 = $this->getMock('IValidationException');
    $e2
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array('errors'=>array($e2))));
    $e = new MultiValidationException(array($e1,$e2));
    $t = $e->to_tree(); 
    $this->assertIsPermutationOf(array($e1,$e2),$t['errors']);
    $this->assertArrayNotHasKey('fields',$t);
  }
  public function testToTreeErrorAndField(){
    $e1 = $this->getMock('IValidationException');
    $e1
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array('errors'=>array($e1))));
    $e2 = $this->getMock('IValidationException');
    $e2
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array('fields'=>array('x'=>array('errors'=>array($e2))))));
    $e = new MultiValidationException(array($e1,$e2));
    $t = $e->to_tree(); 
    $this->assertSame(array(
      'errors'=>array($e1),
      'fields'=>array(
        'x'=>array(
          'errors'=>array($e2),
        ),
      ),
    ),$t);
  }
  public function testToTreeJustFields(){
    for($i=0;$i<10;++$i){
      $es[$i] = $this->getMock('IValidationException');
    }
    $es[0] = $this->getMock('IValidationException');
    $es[0]
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array(
        'fields'=>array(
          'x'=>array(
            'errors'=>array($es[6]),
            'fields'=>array(
              'a'=>array(
                'errors' => array($es[7]),
              ),
            ),
          ),
          'z'=>array(
            'errors'=>array($es[5]),
          ),
          'v'=>array(
            'fields'=>array(
              'a'=>array(
                'errors'=>array($es[8]),
              ),
            ),
          ),
        )
      )));
    $es[1] = $this->getMock('IValidationException');
    $es[1]
      ->expects($this->once())
      ->method('to_tree')
      ->will($this->returnValue(array(
        'fields'=>array(
          'x'=>array(
            'errors'=>array($es[2],$es[3]),
          ),
          'y'=>array(
            'errors'=>array($es[4]),
          ),
          'v'=>array(
            'errors'=>array($es[9]),
          ),
        )
      )));
    $e = new MultiValidationException(array($es[0],$es[1]));
    $t = $e->to_tree(); 
    $xp=array(
      'fields'=>array(
        'x'=>array(
          'errors'=>array($es[6],$es[2],$es[3]),
          'fields'=>array(
            'a'=>array(
              'errors' => array($es[7]),
            ),
          ),
        ),
        'y'=>array(
          'errors'=>array($es[4]),
        ),
        'z'=>array(
          'errors'=>array($es[5]),
        ),
        'v'=>array(
          'errors'=>array($es[9]),
          'fields'=>array(
            'a'=>array(
              'errors'=>array($es[8]),
            ),
          ),
        ),
      ),
    );
    $this->recursive_check($xp,$t);
  }
  private function recursive_check($a,$b){
    $this->assertTrue(array_keys($a)==array_keys($b));
    if(array_key_exists('fields',$a)){
      foreach($a['fields'] as $key => $value){
        $this->recursive_check($value,$b['fields'][$key]);
      }
    }
    if(array_key_exists('errors',$a)){
      $this->assertIsPermutationOf($a['errors'],$b['errors']);
    }
  }
}
?>
