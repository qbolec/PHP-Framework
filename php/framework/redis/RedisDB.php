<?php
class RedisDB implements IRedisDB
{
  private $redis;
  private $persistent;
  private $password;
  private $connected = false;
  public function __construct(IRedis $redis,$host,$port,$ttl,$persistent,$password=null){
    $this->redis = $redis;
    $this->host = $host;
    $this->port = $port;
    $this->ttl = $ttl;
    $this->persistent = $persistent;
    $this->password = $password;
  }
  private function get_redis(){
    if(!$this->connected){
      if($this->persistent){
        $success = $this->redis->pconnect($this->host,$this->port,$this->ttl);
      }else{
        $success = $this->redis->connect($this->host,$this->port,$this->ttl);
      }
      if(!$success){
        throw new CouldNotConnectToRedisException();
      }
      if(null!==$this->password){
        $this->halt_unless($this->redis->auth($this->password));
      }
      $this->connected = true;
    }
    return $this->redis;
  }
  private function halt_unless($b){
    if(!$b){
      Framework::get_instance()->get_assertions()->halt_unless($b);
    }
  }
  //@todo obsługa false 
  public function z_card($key){
    return $this->get_redis()->zCard($key);
  }
  public function z_add($key,$score,$member){
    $this->halt_unless(is_float($score));
    $added = $this->get_redis()->zAdd($key,$score,$member);
    if(false === $added){
      throw new CouldNotConvertException("$key does not hold a sorted set");
    }else{
      return 1==$added;
    }
  }
  public function z_incr_by($key,$score_delta,$member){
    $this->halt_unless(is_float($score_delta));
    $new_score = $this->get_redis()->zIncrBy($key,$score_delta,$member);
    if(false === $new_score){
      throw new CouldNotConvertException("$key does not hold a sorted set");
    }else{
      return (float)($new_score);
    }
  }
  //@todo obsługa false 
  public function z_rev_range($key,$start,$stop){
    return $this->get_redis()->zRevRange($key,$start,$stop,false);
  }
  //@todo obsługa false 
  public function z_rev_range_with_scores($key,$start,$stop){
    return array_map('floatval',$this->get_redis()->zRevRange($key,$start,$stop,true));
  }
  //@todo obsługa false 
  public function z_delete($key,$member){
    return 1==$this->get_redis()->zDelete($key,$member);
  }
  //@todo obsługa false 
  public function delete($key){
    return 1==$this->get_redis()->delete($key);
  }
  public function z_score($key,$member){
    $score = $this->get_redis()->zScore($key,$member);
    if(false === $score){
      throw new IsMissingException("$member in $key");
    }else{
      return (float)($score);
    }
  }
  public function z_scores($key,array $members){
    $redis = $this->get_redis()->multi();
    foreach($members as $member){
      $redis = $redis->zScore($key,$member);
    }
    $results = $redis->exec();
    return array_map('floatval',$this->combine_members_with_results($members,$results));
  }
  public function z_rev_rank($key,$member){
    $rank = $this->get_redis()->zRevRank($key,$member);
    if(false === $rank){
      throw new IsMissingException("$member in $key");
    }else{
      return $rank;
    }
  }
  public function z_rev_ranks($key,array $members){
    $redis = $this->get_redis()->multi();
    foreach($members as $member){
      $redis = $redis->zRevRank($key,$member);
    }
    $results = $redis->exec();
    return $this->combine_members_with_results($members,$results);
  }
  private function combine_members_with_results(array $members,array $results){
    $this->halt_unless(count($members)==count($results));
    $i = 0;
    $answers = array();
    foreach($members as $member){
      $result = $results[$i++];
      if(false !== $result){
        $answers[$member] = $result;
      }
    }
    return $answers;
  }
  public function h_incr_by($key,$field,$delta){
    $this->halt_unless(is_int($delta));
    return $this->get_redis()->hIncrBy($key,$field,$delta);
  }
  public function h_get($key,$field){
    $value = $this->get_redis()->hGet($key,$field);
    if(false === $value){
      throw new IsMissingException("$field in $key");
    }else{
      return $value;
    }
  }
  public function h_set($key,$field,$value){
    $added = $this->get_redis()->hSet($key,$field,$value);
    if(false === $added){
      throw new CouldNotConvertException("$key does not hold a hash");
    }else{
      return (bool)$added;
    }
  }
  public function h_get_all($key){
    $map = $this->get_redis()->hGetAll($key);
    if(false === $map){
      throw new CouldNotConvertException("$key does not hold a hash");
    }else{
      return $map;
    }
  }
  public function incr_by($key,$delta){
    $this->halt_unless(is_int($delta));
    return $this->get_redis()->incrBy($key,$delta);
  }
  public function evaluate(ILUAScript $script,array $keys,array $args){
    $redis = $this->get_redis();
    $redis->clearLastError();
    $ret = $redis->evaluate($script->get_source(),Arrays::concat($keys,$args),count($keys));
    $err = $redis->getLastError();
    if($err){
      $redis->clearLastError();
      throw new CouldNotConvertException($err);
    }
    return $ret;
  }
}
?>
