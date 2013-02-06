<?php
class PrefetchingCacheWrapperTest extends FrameworkTestCase
{
  public function testInterface(){
    $c = $this->getMock('ICache');
    $p = new PrefetchingCacheWrapper($c);
    $this->assertInstanceOf('ICache',$p);
    $this->assertInstanceOf('IPrefetchingCache',$p);
    $this->assertInstanceOf('CacheWrapper',$p);
  }
  public function testGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('a'))
      ->will($this->returnValue(1));
    $c
      ->expects($this->never())
      ->method('multi_get');

    $p = new PrefetchingCacheWrapper($c);
    $this->assertSame(1,$p->get('a'));
  }
  public function testMultiGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('c','d')))
      ->will($this->returnValue(array('c'=>3)));
    $c
      ->expects($this->never())
      ->method('get');

    $p = new PrefetchingCacheWrapper($c);
    $this->assertEquals(array('c'=>3),$p->multi_get(array('c','d')));
  }
  public function testPrefetchingGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->equalTo(array('a','b','c')))
      ->will($this->returnValue(array('a'=>1,'c'=>3)));
    $c
      ->expects($this->never())
      ->method('get');

    $p = new PrefetchingCacheWrapper($c);
    $p->prefetch('a'); 
    $p->prefetch('b'); 
    $p->prefetch('c'); 
    $this->assertSame(1,$p->get('a'));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testPrefetchingGetMiss(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->equalTo(array('a','b','c')))
      ->will($this->returnValue(array('a'=>1,'c'=>3)));
    $c
      ->expects($this->never())
      ->method('get');

    $p = new PrefetchingCacheWrapper($c);
    $p->prefetch('a'); 
    $p->prefetch('b'); 
    $p->prefetch('c'); 
    $p->get('b');
  }
  public function testPrefetchingMultiGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('a','b','c','d')))
      ->will($this->returnValue(array('a'=>1,'c'=>3)));
    $c
      ->expects($this->never())
      ->method('get');

    $p = new PrefetchingCacheWrapper($c);
    $p->prefetch('a'); 
    $p->prefetch('b'); 
    $p->prefetch('c'); 
    $this->assertEquals(array('c'=>3),$p->multi_get(array('c','d')));
  }
  public function testDontTriggerOnEveryGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->never())
      ->method('multi_get');
    $c
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('d'))
      ->will($this->returnValue('x'));

    $p = new PrefetchingCacheWrapper($c);
    $p->prefetch('a'); 
    $p->prefetch('b'); 
    $p->prefetch('c'); 
    $this->assertEquals('x',$p->get('d'));
  }
  public function testDontTriggerOnEveryMultiGet(){
    $c = $this->getMock('ICache');
    $c
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('c','d')))
      ->will($this->returnValue(array('c'=>3)));
    $c
      ->expects($this->never())
      ->method('get');

    $p = new PrefetchingCacheWrapper($c);
    $p->prefetch('a'); 
    $p->prefetch('b'); 
    $this->assertEquals(array('c'=>3),$p->multi_get(array('c','d')));
  }
 
}
?>
