<?php
class LayeredPersistenceManager implements IPersistenceManager
{
  private $near;
  private $far;
  public function __construct(IPersistenceManager $near,IPersistenceManager $far){
    $this->near = $near;
    $this->far = $far;
  }
  public function get_by_id($id){
    try{
      return $this->near->get_by_id($id);
    }catch(NoSuchEntityException $e){
      $result = $this->far->get_by_id($id);
      $this->near->insert($result);
      return $result;
    }
  }
  public function delete_by_id($id){
    $this->near->delete_by_id($id);
    return $this->far->delete_by_id($id);
  }
  public function insert_and_assign_id(array $data_without_id){
    $id = $this->far->insert_and_assign_id($data_without_id);
    $data = array_merge($data_without_id,array(
      'id' => $id,
    ));
    $this->near->insert($data);
    return $id;
  }
  public function insert(array $data){
    if($this->far->insert($data)){
      $this->near->insert($data);
      return true;
    }else{
      return false;
    }
  }
  public function save(array $current_data,array $original_data){
    $this->far->save($current_data,$original_data);
    $this->near->save($current_data,$original_data);
  }
  public function multi_get_by_ids(array $ids){
    $result = $this->near->multi_get_by_ids($ids);
    $misses = array_diff($ids,array_keys($result));
    if(0==count($misses)){
      return $result;
    }else{
      $more = $this->far->multi_get_by_ids($misses);
      foreach($more as $data){
        $this->near->insert($data);
      }
      return Arrays::set_keys_order(Arrays::merge($result,$more),$ids);
    }  
  }
  public function get_fields_descriptor(){
    return $this->far->get_fields_descriptor();
  }
}
?>
