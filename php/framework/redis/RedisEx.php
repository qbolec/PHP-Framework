<?php
class RedisEx extends Redis implements IRedis
{
  public function evaluate($script,$args,$num_keys){
    return $this->eval($script,$args,$num_keys);
  }
}
?>
