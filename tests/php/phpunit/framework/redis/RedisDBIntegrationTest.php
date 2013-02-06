<?php
class RedisDBIntegrationTest extends FrameworkTestCase
{
  private function getSUT($persistent){
    $redis = new RedisEx();
    $host = '127.0.0.1';
    $port = 6379;
    $ttl = 2.5;
    return new RedisDB($redis,$host,$port,$ttl,$persistent);
  }
  /**
   * @group redis
   */
  public function testInterface(){
    $redis_db = $this->getSUT(false);
    $this->assertInstanceOf('IRedisDB',$redis_db);
  }
  /**
   * @group redis
   */
  public function testNonPersistent(){
    $redis = $this->getSUT(false);
  }
  /**
   * @group redis
   */
  public function testPersistent(){
    $redis = $this->getSUT(true);
  }
  /**
   * @group redis
   */
  public function testZDelete(){
    $key = 'somekey';
    $score = 12.0;
    $value = 'someelement';
    $redis = $this->getSUT(false);
    $redis->z_add($key,$score,$value);
    $this->assertSame(true,$redis->z_delete($key,$value));
    $this->assertSame(false,$redis->z_delete($key,$value));
  }

  /**
   * @group redis
   */
  public function testZAdd(){
    $key = 'somekey';
    $score = 12.0;
    $value = 'someelement';
    $redis_db = $this->getSUT(false);
    $redis_db->z_delete($key,$value);
    $this->assertSame(true,$redis_db->z_add($key,$score,$value));
    $this->assertSame(false,$redis_db->z_add($key,$score,$value));
    $this->assertSame(false,$redis_db->z_add($key,$score+1,$value));
  }
  /**
   * @group redis
   */
  public function testDelete(){
    $key = 'somekey';
    $score = 12.0;
    $value = 'someelement';
    $redis = $this->getSUT(false);
    $redis->z_add($key,$score,$value);
    $this->assertSame(true,$redis->delete($key));
    $this->assertSame(false,$redis->delete($key));
  }
  /**
   * @group redis
   */
  public function testZRevRange(){
    $key = 'somekey';
    $start = 0;
    $stop = -1;
    $outcome = array('a','b');
    $redis = $this->getSUT(false);
    $redis->delete($key);
    $redis->z_add($key,7.0,'b');
    $redis->z_add($key,9.0,'a');
    $this->assertSame($outcome,$redis->z_rev_range($key,$start,$stop));
    $this->assertSame(array(),$redis->z_rev_range($key,1,0));
  }
  /**
   * @group redis
   */
  public function testZRevRangeWithScores(){
    $key = 'somekey';
    $start = 0;
    $stop = -1;
    $outcome = array('a'=>10.1,'b'=>3.0);
    $redis = $this->getSUT(false);
    $redis->delete($key);
    $redis->z_add($key,3.0,'b');
    $redis->z_add($key,10.1,'a');
    $result = $redis->z_rev_range_with_scores($key,$start,$stop);
    $this->assertSame($outcome,$result);
    $this->assertSame(array(),$redis->z_rev_range_with_scores($key,1,0));
  }
  public function testZScoreBits(){
    $key = 'somekey';
    $start = 0;
    $stop = -1;
    $outcome = array('a'=>10.1,'b'=>3.0);
    $redis = $this->getSUT(false);
    $bit = 1.0;
    for($i=0;$i<50;++$i){
      $redis->delete($key);
      $redis->z_add($key,$bit,'b');
      $a = $bit + 1.0;
      $redis->z_add($key,$a,'a');
      $this->assertSame($bit,$redis->z_score($key,'b'));
      $this->assertSame($a,$redis->z_score($key,'a'));
      $this->assertSame(array('a'=>$a,'b'=>$bit),$redis->z_rev_range_with_scores($key,0,-1));
      $this->assertNotSame($a,$bit);
      $this->assertSame(1.0,$a-$bit);
      $bit *= 2.0;
    }
  }
}
?>
