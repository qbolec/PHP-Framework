<?php
class MemcacheExTest extends FrameworkTestCase
{
  public function testInterface(){
    $m = $this->getMock('MemcacheEx');
    $this->assertInstanceOf('Memcache',$m);
    $this->assertInstanceOf('IMemcache',$m);
  }
  public function testConnectsToLocalhost(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
  }
  /**
   * @dataProvider scalars
   */
  public function testStoresNonNullScalarsAsString($scalar){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->set('test_key',$scalar,0,10);
    $this->assertSame((string)$scalar,$m->get('test_key')); 
  }
  public function testStoresNullsAsNulls(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->delete('test_key');
    $m->set('test_key',null,0,10);
    $this->assertSame(false,$m->add('test_key',null,0,10));
    $this->assertSame(null,$m->get('test_key')); 
  }
  public function scalars(){
    return array(
      array(''),
      array(1),
      array('1'),
      array(1.0),
      array(true),
      array(false),
      array(1.2),
      array(0),
    );
  }
  public function testIncrementReturnsIntegers(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->set('test_key',13,0,10);
    $this->assertSame(14,$m->increment('test_key')); 
  }
  public function testDecerementReturnsIntegers(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->set('test_key',10,0,10);
    $this->assertSame(9,$m->decrement('test_key')); 
  }
  public function testGetAfterDecrementReturnsPaddedStrings(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->set('test_key',10,0,10);
    $this->assertSame(9,$m->decrement('test_key')); 
    $res=$m->get('test_key');
    $this->assertSame("9 ",$res); 
    $this->assertSame(9,json_decode(rtrim($res))); 
  }
  public function testIncrementSignalsMissesWithFalse(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->delete('test_key');
    $this->assertSame(false,$m->increment('test_key')); 
  }
  public function testMultiGetSignalsMissesWithMisses(){
    $m = new MemcacheEx();
    $m->addServer('127.0.0.1',11211);
    $m->delete('test_key');
    $this->assertSame(array(),$m->get(array('test_key')));
  }
  public function testAddingServerDoesNotTryToConnect(){
    $m = new MemcacheEx();
    $m->addServer('nosuchhost',11211);
  }
}
?>
