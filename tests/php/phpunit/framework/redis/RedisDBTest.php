<?php
class RedisDBTest extends FrameworkTestCase
{
  public function testInterface(){
    $redis = $this->getMock('IRedis');
    $redis_db = new RedisDB($redis,'127.0.0.1',1234,2.5,false);
    $this->assertInstanceOf('IRedisDB',$redis_db);
  }
  public function testNonPersistent(){
    $redis = $this->getMock('IRedis');
    $host = '127.0.0.1';
    $port = 1234;
    $ttl = 2.4;
    $redis
      ->expects($this->once())
      ->method('connect')
      ->with($this->equalTo($host),$this->equalTo($port),$this->equalTo($ttl))
      ->will($this->returnValue(true));

    $redis_db = new RedisDB($redis,$host,$port,$ttl,false);
    $redis_db->z_card('whatever');
  }
  public function testPersistent(){
    $redis = $this->getMock('IRedis');
    $host = '127.0.0.1';
    $port = 1234;
    $ttl = 2.4;
    $redis
      ->expects($this->once())
      ->method('pconnect')
      ->with($this->equalTo($host),$this->equalTo($port),$this->equalTo($ttl))
      ->will($this->returnValue(true));

    $redis_db = new RedisDB($redis,$host,$port,$ttl,true);
    $redis_db->z_card('whatever');
  }
  /**
   * @expectedException CouldNotConnectToRedisException
   */
  public function testNonPersistentConnectionFailure(){
    $redis = $this->getMock('IRedis');
    $host = '127.0.0.1';
    $port = 1234;
    $ttl = 2.4;
    $redis
      ->expects($this->once())
      ->method('connect')
      ->with($this->equalTo($host),$this->equalTo($port),$this->equalTo($ttl))
      ->will($this->returnValue(false));

    $redis_db = new RedisDB($redis,$host,$port,$ttl,false);
    $redis_db->z_card('whatever');
  }
  /**
   * @expectedException CouldNotConnectToRedisException
   */
  public function testPersistentConnectionFailure(){
    $redis = $this->getMock('IRedis');
    $host = '127.0.0.1';
    $port = 1234;
    $ttl = 2.4;
    $redis
      ->expects($this->once())
      ->method('pconnect')
      ->with($this->equalTo($host),$this->equalTo($port),$this->equalTo($ttl))
      ->will($this->returnValue(false));

    $redis_db = new RedisDB($redis,$host,$port,$ttl,true);
    $redis_db->z_card('whatever');
  }
  private function getSUT($redis){
    return new RedisDB($redis,'127.0.0.1',1234,2.4,false);
  }
  public function zeroOrOne(){
    return array(
      array(0),
      array(1),
    );
  }
  public function getMockRedis(){
    $redis = $this->getMock('IRedis');
    $redis
      ->expects($this->once())
      ->method('connect')
      ->with($this->equalTo('127.0.0.1'),$this->equalTo(1234),$this->equalTo(2.4))
      ->will($this->returnValue(true)); 
    return $redis;
  }
  /**
   * @dataProvider zeroOrOne
   */
  public function testZAdd($outcome){
    $key = 'somekey';
    $score = 12.0;
    $value = 'someelement';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zAdd')
      ->with($this->equalTo($key),$this->equalTo($score),$this->equalTo($value))
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame((bool)$outcome,$redis_db->z_add($key,$score,$value));
  }
  /**
    * @expectedException CouldNotConvertException
    */
  public function testAlreadyMemberZAdd(){
    $key = 'somekey';
    $score = 12.0;
    $value = 'someelement';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zAdd')
      ->with($this->equalTo($key),$this->equalTo($score),$this->equalTo($value))
      ->will($this->returnValue(false));
    $redis_db = $this->getSUT($redis);
    $redis_db->z_add($key,$score,$value);
  }
  /**
    * @expectedException LogicException
    */
  public function testZAddWithWrongArguments(){
    $key = 'somekey';
    $score = 12;
    $value = 'someelement';
    $redis = $this->getMock('IRedis');
    $redis_db = $this->getSUT($redis);
    $redis_db->z_add($key,$score,$value);
  }
  public function testZRevRange(){
    $key = 'somekey';
    $start = 0;
    $stop = -1;
    $outcome = array('a','b');
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zRevRange')
      ->with($this->equalTo($key),$this->equalTo($start),$this->equalTo($stop),false)
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame($outcome,$redis_db->z_rev_range($key,$start,$stop));
  }
  public function testZRevRangeWithScores(){
    $key = 'somekey';
    $start = 0;
    $stop = -1;
    $outcome = array('a'=>10.0,'b'=>3.01);
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zRevRange')
      ->with($this->equalTo($key),$this->equalTo($start),$this->equalTo($stop),true)
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame($outcome,$redis_db->z_rev_range_with_scores($key,$start,$stop));
  }
  /**
   * @dataProvider zeroOrOne
   */
  public function testZDelete($outcome){
    $key = 'somekey';
    $value = 'someelement';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zDelete')
      ->with($this->equalTo($key),$this->equalTo($value))
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame((bool)$outcome,$redis_db->z_delete($key,$value));
  }
  /**
   * @dataProvider zeroOrOne
   */
  public function testDelete($outcome){
    $key = 'somekey';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('delete')
      ->with($this->equalTo($key))
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame((bool)$outcome,$redis_db->delete($key));
  }
  /**
   * @dataProvider zeroOrOne
   */
  public function testZCard($outcome){
    $key = 'somekey';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zCard')
      ->with($this->equalTo($key))
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame($outcome,$redis_db->z_card($key));
  }
  public function testZScore(){
    $value = 42.0;
    $key = 'somekey';
    $member = 'somemember';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zScore')
      ->with($this->equalTo($key),$this->equalTo($member))
      ->will($this->returnValue($value));
    $redis_db = $this->getSUT($redis);
    $this->assertSame(42.0,$redis_db->z_score($key,$member));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testMissZScore(){
    $value = false;
    $key = 'somekey';
    $member = 'somemember';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zScore')
      ->with($this->equalTo($key),$this->equalTo($member))
      ->will($this->returnValue($value));
    $redis_db = $this->getSUT($redis);
    $redis_db->z_score($key,$member);
  }
  public function testZRevRank(){
    $value = 42;
    $key = 'somekey';
    $member = 'somemember';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zRevRank')
      ->with($this->equalTo($key),$this->equalTo($member))
      ->will($this->returnValue($value));
    $redis_db = $this->getSUT($redis);
    $this->assertSame(42,$redis_db->z_rev_rank($key,$member));
  }
  /**
   * @expectedException IsMissingException
   */
  public function testMissZRevRank(){
    $value = false;
    $key = 'somekey';
    $member = 'somemember';
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zRevRank')
      ->with($this->equalTo($key),$this->equalTo($member))
      ->will($this->returnValue($value));
    $redis_db = $this->getSUT($redis);
    $redis_db->z_rev_rank($key,$member);
  }
  public function testZIncrBy(){
    $key = 'somekey';
    $member = 'somemember';
    $score_delta = 42.0;
    $outcome = 43.0;
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zIncrBy')
      ->with($this->equalTo($key),$this->equalTo($score_delta),$this->equalTo($member))
      ->will($this->returnValue($outcome));
    $redis_db = $this->getSUT($redis);
    $this->assertSame($outcome,$redis_db->z_incr_by($key,$score_delta,$member));
  }
  /**
    * @expectedException CouldNotConvertException
    */
  public function testMissZIncrBy(){
    $key = 'somekey';
    $member = 'somemember';
    $score_delta = 42.0;
    $redis = $this->getMockRedis();
    $redis
      ->expects($this->once())
      ->method('zIncrBy')
      ->with($this->equalTo($key),$this->equalTo($score_delta),$this->equalTo($member))
      ->will($this->returnValue(false));
    $redis_db = $this->getSUT($redis);
    $redis_db->z_incr_by($key,$score_delta,$member);
  }
  /**
   * @expectedException LogicException
   */
  public function testZIncrByCalledWithWrongArguments(){
    $key = 'somekey';
    $member = 'somemember';
    $score_delta = 'cztery';
    $redis = $this->getMock('IRedis');
    $redis_db = $this->getSUT($redis);
    $redis_db->z_incr_by($key,$score_delta,$member);
  }
}
?>
