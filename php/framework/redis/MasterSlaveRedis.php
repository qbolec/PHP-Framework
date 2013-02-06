<?php
class MasterSlaveRedis implements IRedisDB
{
  private $master;
  private $slave;
  public function __construct(IRedisDB $master,IRedisDB $slave){
    $this->master = $master;
    $this->slave = $slave;
  }
  public function z_card($key){
    return $this->slave->z_card($key);
  }
  public function z_add($key,$score,$member){
    return $this->master->z_add($key,$score,$member);
  }
  public function z_incr_by($key,$score_delta,$member){
    return $this->master->z_incr_by($key,$score_delta,$member);
  }
  public function z_rev_range($key,$start,$stop){
    return $this->slave->z_rev_range($key,$start,$stop);
  }
  public function z_rev_range_with_scores($key,$start,$stop){
    return $this->slave->z_rev_range_with_scores($key,$start,$stop);
  }
  public function z_delete($key,$member){
    return $this->master->z_delete($key,$member);
  }
  public function delete($key){
    return $this->master->delete($key);
  }
  public function z_score($key,$member){
    return $this->slave->z_score($key,$member);
  }
  public function z_scores($key,array $members){
    return $this->slave->z_scores($key,$members);
  }
  public function z_rev_rank($key,$member){
    return $this->slave->z_rev_rank($key,$member);
  }
  public function z_rev_ranks($key,array $members){
    return $this->slave->z_rev_ranks($key,$members);
  }
  public function h_incr_by($key,$field,$delta){
    return $this->master->h_incr_by($key,$field,$delta);
  }
  public function h_get($key,$field){
    return $this->slave->h_get($key,$field);
  }
  public function h_set($key,$field,$value){
    return $this->master->h_set($key,$field,$value);
  }
  public function h_get_all($key){
    return $this->slave->h_get_all($key);
  }
  public function incr_by($key,$delta){
    return $this->master->incr_by($key,$delta);
  }
}
?>
