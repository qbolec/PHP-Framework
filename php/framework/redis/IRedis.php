<?php
interface IRedis
{
  //public function connect($host,$port=6379,$ttl=0);
  public function connect();
  //public function pconnect($host,$port=6379,$ttl=0,$persistent_id=null);
  public function pconnect();
  //public function zAdd($key,$score,$member);
  public function zAdd();
  //public function zIncrBy($key,$score_delta,$member);
  public function zIncrBy();
  //public function zRevRange($key,$start,$stop,$with_scores);
  public function zRevRange();
  //public function zDelete($key,$member)
  public function zDelete();
  //public function delete($key)
  public function delete();
  //public function zCard($key);
  public function zCard();
  //public function hSet($key,$field,$value);
  public function hSet();
  //public function hGet($key,$field);
  public function hGet();
  //public function hGetAll($key)
  public function hGetAll();
  //public function hIncrBy($key,$field,$delta);
  public function hIncrBy();  
  //public function incrBy($key,$delta);
  public function incrBy();
  public function zScore();
  public function zRevRank();
  //public function auth($password)
  public function auth();
  //public function multi(MULTI | PIPELINE);
  public function multi();
  public function exec();
}
?>
