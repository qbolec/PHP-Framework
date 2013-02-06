<?php
class LayeredCacheTest extends FrameworkTestCase
{
  public function testInterface(){
    $n = $this->getMock('ICache');
    $f = $this->getMock('ICache');
    $c = new LayeredCache($n,$f);
    $this->assertInstanceOf('ICache',$c);
  }
  public function testGetTriesNearFirst(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue('x'));
    $f = $this->getMock('ICache');
    $f
      ->expects($this->never())
      ->method('get');
    $c = new LayeredCache($n,$f);
    $this->assertSame('x',$c->get('test_key'));
  }
  public function testGetTriesFarThen(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->throwException(new IsMissingException('test_key')));
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo('x'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue('x'));
    $c = new LayeredCache($n,$f);
    $this->assertSame('x',$c->get('test_key'));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testGetMisses(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->throwException(new IsMissingException('test_key')));
    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('test_key'))
      ->will($this->throwException(new IsMissingException('test_key')));
    $c = new LayeredCache($n,$f);
    $c->get('test_key');
  }
  public function testMultiGetTriesNearThenFar(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('a','b','c')))
      ->will($this->returnValue(array('a'=>1)));
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('b'),$this->equalTo(2));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('b','c')))
      ->will($this->returnValue(array('b'=>2)));

    $c = new LayeredCache($n,$f);
    $this->assertEquals(array('a'=>1,'b'=>2),$c->multi_get(array('a','b','c')));
  }
  public function testMultiGetDoesntBotherFar(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('multi_get')
      ->with($this->isPermutationOf(array('a','b')))
      ->will($this->returnValue(array('a'=>1,'b'=>2)));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->never())
      ->method('multi_get');

    $c = new LayeredCache($n,$f);
    $this->assertEquals(array('a'=>1,'b'=>2),$c->multi_get(array('a','b')));
  }
  
  public function testSetChangesBoth(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo('x'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo('x'));

    $c = new LayeredCache($n,$f);
    $c->set('test_key','x'); 
  } 
  public function testSuccessfulAddPropagatesAsSet(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo('x'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('test_key'),$this->equalTo('x'))
      ->will($this->returnValue(true));

    $c = new LayeredCache($n,$f);
    $this->assertSame(true,$c->add('test_key','x')); 
  } 
  public function testUnsuccessfulAddPropagatesAsDelete(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('test_key'),$this->equalTo('x'))
      ->will($this->returnValue(false));

    $c = new LayeredCache($n,$f);
    $this->assertSame(false,$c->add('test_key','x')); 
  }
  /**
   * @expectedException IsMissingException
   */
  public function testUnsuccessfulIncrementPropagatesAsDelete(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('test_key'),$this->equalTo(1))
      ->will($this->throwException(new IsMissingException('test_key')));

    $c = new LayeredCache($n,$f);
    $c->increment('test_key',1); 
  }

  public function testSuccessfulIncrementPropagatesAsSet(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo(2));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('test_key'),$this->equalTo(1))
      ->will($this->returnValue(2));

    $c = new LayeredCache($n,$f);
    $this->assertSame(2,$c->increment('test_key',1)); 
  }
  /**
   * @dataProvider getBool
   */
  public function testDeletePropagatesAsDelete($bool){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue(true));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'))
      ->will($this->returnValue($bool));

    $c = new LayeredCache($n,$f);
    $this->assertSame($bool,$c->delete('test_key')); 
  }
  public function getBool(){
    return array(
      array(true),
      array(false),
    );
  }
  /**
   * @expectedException IsMissingException
   */
  public function testUnsuccessfulIncrementOrAddPropagatesAsDelete(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo('test_key'));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('increment_or_add')
      ->with($this->equalTo('test_key'),$this->equalTo(1),$this->equalTo(3))
      ->will($this->throwException(new IsMissingException('test_key')));

    $c = new LayeredCache($n,$f);
    $c->increment_or_add('test_key',1,3); 
  }

  public function testSuccessfulIncrementOrAddPropagatesAsSet(){
    $n = $this->getMock('ICache');
    $n
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('test_key'),$this->equalTo(2));

    $f = $this->getMock('ICache');
    $f
      ->expects($this->once())
      ->method('increment_or_add')
      ->with($this->equalTo('test_key'),$this->equalTo(1),$this->equalTo(3))
      ->will($this->returnValue(2));

    $c = new LayeredCache($n,$f);
    $this->assertSame(2,$c->increment_or_add('test_key',1,3)); 
  }

}
?>
