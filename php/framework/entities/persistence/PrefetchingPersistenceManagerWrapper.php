<?php
class PrefetchingPersistenceManagerWrapper extends PersistenceManagerWrapper implements IPrefetchingPersistenceManager
{
  //@todo remove copy&paste from PrefetchingCacheWrapper
  //@todo drop the optimization and always call parent:: directly?
  private $queue = array();
  public function get_by_id($id){
    if(array_key_exists($id,$this->queue)){
      $results = $this->execute_download();
      if(array_key_exists($id,$results)){
        return $results[$id];
      }else{
        throw new NoSuchEntityException($id);
      }
    }
    return parent::get_by_id($id);
  }
  public function multi_get_by_ids(array $ids){
    $hashed = array_flip($ids);
    if(0!=count(Arrays::intersect_key($hashed,$this->queue))){
      $this->queue = Arrays::merge($this->queue,$hashed);
      $results = $this->execute_download();
      return Arrays::set_keys_order(Arrays::intersect_key($results,$hashed),$ids);
    }
    return parent::multi_get_by_ids($ids);
  }
  public function prefetch_by_id($id){
    $this->queue[$id]=true;
  }
  private function execute_download(){
    $res = parent::multi_get_by_ids(array_keys($this->queue));
    $this->queue = array();
    return $res;
  }
}
?>
