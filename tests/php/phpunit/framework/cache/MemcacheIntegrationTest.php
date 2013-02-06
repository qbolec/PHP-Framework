<?php
class MemcacheIntegrationTest extends FrameworkTestCase
{
  private function getSUT(){
    $servers=array(
      array(
        'host' => 'localhost',
        'port' => 11211,
      ),
    );
    return new MemcacheCache($servers,10);
  }
  public function storeable(){
    return array(
      array(''),
      array('ala'),
      array('1'),
      array('1.0'),
      array(1),
      array(1.0),
      array(0),
      array(false),
      array(null),
      array(true),
      array(array()),
      array(array(0)),  
    );
  }
  /**
   * @dataProvider storeable
   */
  public function testSetGetPreservesTypes($storable){
    $m = $this->getSUT();
    $m->set('test_key',$storable);
    $this->assertSame($storable,$m->get('test_key'));
  }
  /**
   * @dataProvider storeable
   */
  public function testAddGetPreservesTypes($storable){
    $m = $this->getSUT();
    $m->delete('test_key');
    $this->assertSame(true,$m->add('test_key',$storable));
    $this->assertSame($storable,$m->get('test_key'));
  }
  public function testIncrementReturnsIntegers(){
    $m = $this->getSUT();
    $m->delete('test_key');
    $this->assertSame(true,$m->add('test_key',42));
    $this->assertSame(43,$m->increment('test_key',1));
  }
  public function testHandlesSpacesCorrectly(){
    $m = $this->getSUT();
    $m->delete('test_key');
    $this->assertSame(true,$m->add('test_key',9));
    $this->assertSame(10,$m->increment('test_key',1));
    $this->assertSame(10,$m->get('test_key'));
    $this->assertSame(9,$m->increment('test_key',-1));
    $this->assertSame(9,$m->get('test_key'));
    $this->assertSame(8,$m->increment('test_key',-1));
    $this->assertSame(8,$m->get('test_key'));
    $this->assertSame(9,$m->increment('test_key',1));
    $this->assertSame(9,$m->get('test_key'));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testGetSignalsMissesWithExceptions(){
    $m = $this->getSUT();
    $m->delete('test_key');
    $m->get('test_key');
  }

  public function testMultiGet(){
    $m = $this->getSUT();
    $m->delete('test_key');
    $m->set('test_key2','x');
    $this->assertSame(array('test_key2'=>'x'),$m->multi_get(array('test_key','test_key2')));
  }

  
}
?>
