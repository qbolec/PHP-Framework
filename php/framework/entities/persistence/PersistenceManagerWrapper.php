<?php
class PersistenceManagerWrapper implements IPersistenceManager
{
  private $pm;
  public function __construct(IPersistenceManager $pm){
    $this->pm = $pm;
  }
  public function get_by_id($id){
    return $this->pm->get_by_id($id);
  }
  public function delete_by_id($id){
    return $this->pm->delete_by_id($id);
  }
  public function insert(array $data){
    return $this->pm->insert($data);
  }
  public function insert_and_assign_id(array $data_without_id){
    return $this->pm->insert_and_assign_id($data_without_id);
  }
  public function save(array $new_data,array $old_data){
    return $this->pm->save($new_data,$old_data);
  }
  public function multi_get_by_ids(array $ids){
    return $this->pm->multi_get_by_ids($ids);
  }
  public function get_fields_descriptor(){
    return $this->pm->get_fields_descriptor();
  }
}
?>
