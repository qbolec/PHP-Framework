<?php
class CacheWrapperTest extends FrameworkTestCase
{
  public function testInterface(){
    $c = $this->getMock('ICache');
    $cw = new CacheWrapper($c);
    $this->assertInstanceOf('ICache',$cw);
  }
  public function testForwardsGet(){
    $c = $this->getMock('ICache');
    $res = 42;
    $c
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->get('test_key'));
  }
  public function testForwardsMultiGet(){
    $c = $this->getMock('ICache');
    $res = array('test_key'=>42);
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->equalTo(array('test_key')))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->multi_get(array('test_key')));
  }
  public function testForwardsSet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo('x'));
    $cw = new CacheWrapper($c);
    $cw->set('test_key','x');
  }
  public function testForwardsAdd(){
    $res = true;
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('test_key'),$this->equalTo('x'))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->add('test_key','x'));
  }
  public function testForwardsDelete(){
    $res = true;
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->delete('test_key'));
  }
  public function testForwardsIncrement(){
    $res = 43;
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('test_key'),$this->equalTo(42))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->increment('test_key',42));
  }
  public function testForwardsIncrementOrAdd(){
    $res = 43;
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('increment_or_add')
      ->with($this->equalTo('test_key'),$this->equalTo(42),$this->equalTo(13))
      ->will($this->returnValue($res));
    $cw = new CacheWrapper($c);
    $this->assertSame($res,$cw->increment_or_add('test_key',42,13));
  }
}
?>
