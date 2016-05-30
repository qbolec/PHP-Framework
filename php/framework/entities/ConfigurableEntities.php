<?php
abstract class ConfigurableEntities extends AbstractEditablePersistentEntities
{

  private $pool = array();
  private $pool_config = array();
  public function __construct($config_name){
    try{
      $this->pool_config = Config::get_instance()->get('entities/' . $config_name . '/pool');
    }catch(IsMissingException $e){
    }
    parent::__construct(
      Framework::get_instance()
        ->get_persistence_manager_factory()
        ->from_config_name_and_descriptor($config_name,$this->get_fields_descriptor())
    );
  }
  private function log($info){
    //305 ms -> 240ms dla /history/progress gdy wyłącze logowanie które nawet nic nie robi:)
    //Framework::get_instance()->get_logger()->log($info);
  }
  private function touch($id){
    //to powinno przesunąć obiekt na koniec wewnętrznej listy arraya,
    //co w efekcie daje nam to, że MRU są na końcu tablicy
    $entity=$this->pool[$id];
    unset($this->pool[$id]);
    $this->pool[$id] = $entity;
  }
  private function clamp(){
    $this->log(array_keys($this->pool));
    $max_size = Arrays::get($this->pool_config,'max_size');
    if(null!==$max_size && $max_size<count($this->pool)){
      $min_size = Arrays::get($this->pool_config,'min_size',$max_size);
      $this->pool = 0 == $min_size ? array() : array_slice($this->pool,-$min_size,null,true);
    }
  }
  public function get_by_id($id){
    if(!array_key_exists($id,$this->pool)){
      $entity = $this->pool[$id] = parent::get_by_id($id);
      $this->clamp();
      return $entity;
    }else{
      $this->log($id);
      $this->touch($id);
      return $this->pool[$id];
    }
  }
  public function multi_get_by_ids(array $ids){
    $misses = array_diff($ids,array_keys($this->pool));
    $this->log(array('hits'=>count($ids)-count($misses),'misses'=>count($misses)));
    $far = parent::multi_get_by_ids($misses);
    foreach($far as $id => $entity){
      $this->pool[$id] = $entity;
    }
    $result = array();
    foreach($ids as $id){
      if(array_key_exists($id, $this->pool)){
        $this->touch($id);
        $result[$id] = $this->pool[$id];
      }
    }
    $this->clamp();
    return $result;
  }
}
?>
