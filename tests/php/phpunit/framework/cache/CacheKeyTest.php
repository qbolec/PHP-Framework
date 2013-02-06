<?php
class CacheKeyTest extends FrameworkTestCase
{
  private function getCache(){
    $cache = $this->getMock('IPrefetchingCache');
    return $cache;
  }
  private function getSUT($cache){
    return new CacheKey($cache,'test-key');
  }
  public function testInterface(){
    $cache = $this->getCache();
    $c = new CacheKey($cache,'x');
    $this->assertInstanceOf('ICacheKey',$c);
  }
  public function testConstructDoesNotHaveSideEffects(){
    $cache = $this->getCache();
    $c = new CacheKey($cache,'atlantis');
    $this->assertInstanceOf('ICacheKey',$c);
  }
  public function testForwardsGet(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test-key'))
      ->will($this->returnValue(42));
    $c = $this->getSUT($cache);
    $this->assertSame(42,$c->get());
  }
  public function testForwardsSet(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test-key'),$this->equalTo('a'));
    $c = $this->getSUT($cache);
    $c->set('a');
  }
  public function testForwardsAdd(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('test-key'),$this->equalTo('a'))
      ->will($this->returnValue(true));
    $c = $this->getSUT($cache);
    $this->assertSame(true,$c->add('a'));
  }
  public function testForwardsDelete(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test-key'))
      ->will($this->returnValue(true));
    $c = $this->getSUT($cache);
    $this->assertSame(true,$c->delete());
  }
  public function testForwardsIncrement(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('test-key'),$this->equalTo(7))
      ->will($this->returnValue(42));
    $c = $this->getSUT($cache);
    $this->assertSame(42,$c->increment(7));
  }
  public function testForwardsIncrementOrAdd(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('increment_or_add')
      ->with($this->equalTo('test-key'),$this->equalTo(7),$this->equalTo(2))
      ->will($this->returnValue(42));
    $c = $this->getSUT($cache);
    $this->assertSame(42,$c->increment_or_add(7,2));
  }
  public function testForwardsPrefetch(){
    $cache = $this->getMock('IPrefetchingCache');
    $cache
      ->expects($this->once())
      ->method('prefetch')
      ->with($this->equalTo('test-key'));
    $c = $this->getSUT($cache);
    $c->prefetch();
  }
}
?>
