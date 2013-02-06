<?php
class ArrayCacheTest extends FrameworkTestCase
{
  public function testInterface(){
    $a = new ArrayCache();
    $this->assertInstanceOf('ICache',$a);
  }
  public function values(){
    return array(
      array(1),
      array('1'),
      array(1.0),
      array(true),
      array(false),
      array(1.2),
      array(0),
      array(null),
      array(array()),
      array(array(null,1)),
      array(""),
    );
  }
 /**
   * @dataProvider values
   */
  public function testSetGet($value){
    $a = new ArrayCache();
    $a->set('test_key',$value);
    $this->assertSame($value,$a->get('test_key'));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testDeleteGet(){
    $a = new ArrayCache();
    $a->delete('test_key');
    $a->get('test_key');
  }
  /**
   * @expectedException IsMissingException
   */
  public function testDeleteIncrement(){
    $a = new ArrayCache();
    $a->delete('test_key');
    $a->increment('test_key',1);
  }
  public function testDeleteMultiGet(){
    $a = new ArrayCache();
    $a->delete('test_key');
    $this->assertSame(array(),$a->multi_get(array('test_key')));
  }
  public function testSetDelete(){
    $a = new ArrayCache();
    $a->set('test_key','x');
    $this->assertSame(true,$a->delete('test_key'));
    $this->assertSame(false,$a->delete('test_key'));
  }
  public function testIncrement(){
    $a = new ArrayCache();
    $a->set('test_key',10);
    $this->assertSame(9,$a->increment('test_key',-1));
    $this->assertSame(9,$a->get('test_key'));
  }
  public function testIncrementOrAdd(){
    $a = new ArrayCache();
    $this->assertSame(2,$a->increment_or_add('test_key',1,2));
    $this->assertSame(3,$a->increment_or_add('test_key',1,2));
  }
  public function testAdd(){
    $a = new ArrayCache();
    $a->delete('test_key');
    $this->assertSame(true,$a->add('test_key',null));
    $this->assertSame(false,$a->add('test_key',null));
    $this->assertSame(null,$a->get('test_key'));
  }
}
?>
