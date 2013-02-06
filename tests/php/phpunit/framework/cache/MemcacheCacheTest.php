<?php
class FakeCache extends MemcacheCache
{
  private $mock_client;
  public function __construct(IMemcache $client,array $params){
    $this->mock_client = $client;
    parent::__construct($params['servers'],$params['ttl']);
  } 
  protected function get_client(){
    return $this->mock_client;
  }
}
class MemcacheCacheTest extends FrameworkTestCase
{
  private function goodParams(){
   return array(
      'servers'=>array(
        array(
          'host' => 'localhost',
          'port' => 11211,
        ),
      ),
      'ttl' => 69,
    );
  }
  private function spawn(IMemcache $memcache,array $params){
    return new FakeCache($memcache,$params);
  }
  public function testInterface(){
    $memcache = $this->getMock('IMemcache');
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertInstanceOf('ICache',$memcache_cache);
  }
  /**
   * @dataProvider badParams
   * @expectedException LogicException
   */
  public function testValidatesParams($params){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->never())
      ->method('addServer');
    $memcache_cache = $this->spawn($memcache,$params);
  }
  public function badParams(){
    return array(
      array(array('servers'=>array(),'ttl'=>0)),
      array(array('servers'=>array('localhost',11211),'ttl'=>0)),
      array(array('servers'=>array(
        'host' => 'localhost',
      ),'ttl'=>0)),
      array(array('servers'=>array('localhost',11211),'ttl'=>0)),
      array(array('servers'=>array(
        'host' => 'localhost',
        'port' => '11211',
      ),'ttl'=>0)),
      array(array('servers'=>array(
        'host' => 'localhost',
        'port' => 11211,
      ),'ttl'=>0)),
      array(array('servers'=>array(array(
        'host' => 'localhost',
        'port' => '11211',
      )),'ttl'=>0)),
      array(array('servers'=>array(array(
        'host' => 'localhost',
        'port' => 11211,
      )),'ttl'=> -10)),
     );
  }
  public function testAddsServers(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('addServer')
      ->with($this->equalTo('localhost'),$this->equalTo(11211));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
  }
  public function testForwardsSet(){
    $params = $this->goodParams();
    $ttl = $params['ttl'];
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('set')
      ->with($this->equalTo('key'),$this->stringContains('value'),$this->anything(),$this->equalTo($ttl));
    $memcache_cache = $this->spawn($memcache,$params);
    $memcache_cache->set('key','value');
  }
  public function testForwardsAdd(){
    $params = $this->goodParams();
    $ttl = $params['ttl'];
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('key'),$this->stringContains('value'),$this->anything(),$this->equalTo($ttl))
      ->will($this->returnValue(false));
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(false,$memcache_cache->add('key','value',42));
  }
  public function testForwardsGet(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('key'))
      ->will($this->returnValue('42'));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(42,$memcache_cache->get('key'));
  }
  public function testForwardsMultiGet(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('get')
      ->with($this->isPermutationOf(array('a','b')))
      ->will($this->returnValue(array('a'=>1,'b'=>2)));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertEquals(array('a'=>1,'b'=>2),$memcache_cache->multi_get(array('a','b')));
  }
  public function testDoesNotBotherWithEmptyMultiGet(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->never())
      ->method('get');
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertEquals(array(),$memcache_cache->multi_get(array()));
  }
  public function multiGetBadAnswer(){
    return array(
      array(array('key'),false,array()),
      array(array('a','b'),array('a'=>'1','b'=>false),array('a'=>1)),
    );
  }
  /**
   * @dataProvider multiGetBadAnswer
   */
  public function testMultiGetWarns($keys,$values,$expected_result){
    $logger = $this->getMock('ILogger');
    $logger
      ->expects($this->exactly(2))
      ->method('log');
    
    $framework = $this->getMock('Framework',array('get_logger'));
    $framework
      ->expects($this->once())
      ->method('get_logger')
      ->will($this->returnValue($logger));

    $this->set_global_mock('Framework',$framework);

    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo($keys))
      ->will($this->returnValue($values));
    $params = $this->goodParams();
    
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame($expected_result,$memcache_cache->multi_get($keys));
    

    
  }
  /**
   * @expectedException IsMissingException
   */
  public function testConvertsMissesToExceptions(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(false));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(null,$memcache_cache->get('key'));
  }
  public function testForwardsPositiveIncrements(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(11));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(11,$memcache_cache->increment('key',1));
  }
  public function testTranslatesNegativeIncrements(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('decrement')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(9));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(9,$memcache_cache->increment('key',-1));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testTranslatesIncrementMissesToExceptions(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(false));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(null,$memcache_cache->increment('key',1));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testTranslatesDecrementMissesToExceptions(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('decrement')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(false));
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(null,$memcache_cache->increment('key',-1));
  }
  public function testIncrementOrAdd1(){
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(12));
    $memcache
      ->expects($this->never())
      ->method('add');
    $params = $this->goodParams();
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(12,$memcache_cache->increment_or_add('key',1,42));
  }
  public function testIncrementOrAdd2(){
    $params = $this->goodParams();
    $ttl = $params['ttl'];
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->once())
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(false));
    $memcache
      ->expects($this->once())
      ->method('add')
      ->with($this->equalTo('key'),$this->equalTo(42),$this->anything(),$this->equalTo($ttl))
      ->will($this->returnValue(true));
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(42,$memcache_cache->increment_or_add('key',1,42));
  }
  public function testIncrementOrAdd3(){
    $params = $this->goodParams();
    $ttl = $params['ttl'];
    $memcache = $this->getMock('IMemcache');
    $memcache
      ->expects($this->at(0))
      ->method('addServer');
    $memcache
      ->expects($this->at(1))
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(false));
    $memcache
      ->expects($this->at(2))
      ->method('add')
      ->with($this->equalTo('key'),$this->equalTo(42),$this->anything(),$this->equalTo($ttl))
      ->will($this->returnValue(false));
    $memcache
      ->expects($this->at(3))
      ->method('increment')
      ->with($this->equalTo('key'))
      ->will($this->returnValue(44));
    $memcache_cache = $this->spawn($memcache,$params);
    $this->assertSame(44,$memcache_cache->increment_or_add('key',1,42));
  }
  private $fake_data=array();
  /**
   * @dataProvider storeable
   */
  public function testPreservesTypes($data){ 
    $params = $this->goodParams();
    $ttl = $params['ttl'];
    $memcache = $this->getMock('IMemcache');
    $this->fake_data = array();
    $memcache
      ->expects($this->any())
      ->method('set')
      ->with($this->equalTo('key'),$this->anything(),$this->anything(),$this->equalTo($ttl))
      ->will($this->returnCallback(array($this,'fake_set')));
    $memcache
      ->expects($this->any())
      ->method('get')
      ->with($this->equalTo('key'))
      ->will($this->returnCallback(array($this,'fake_get')));
    $memcache_cache = $this->spawn($memcache,$params);
    $memcache_cache->set('key',$data);
    $this->assertSame($data,$memcache_cache->get('key'));
  }
  public function fake_set($key,$value,$flags,$ttl){
    $this->fake_data[$key]=array($value,$flags);
  }
  /**
   * tak gdzieś po drodze scalary stają się stringami
   * a inne obiekty są serializowane poprawnie
   */
  public function fake_get($key){
    list($value,$flags) = $this->fake_data[$key];
    if(is_string($value)||is_long($value)||is_double($value)||is_bool($value)){
      return (string)$value;
    }else{
      return $value;
    }
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
}
?>
