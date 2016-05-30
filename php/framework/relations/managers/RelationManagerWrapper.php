<?php
class RelationManagerWrapper implements IRelationManager
{
  private $relation_manager;
  public function __construct(IRelationManager $relation_manager){
    $this->relation_manager = $relation_manager;
  }
  public function get_count(array $key){
    return $this->relation_manager->get_count($key);
  }
  public function get_all(array $key=array(),array $order_by=array(),$limit=null,$offset=null){
    return $this->relation_manager->get_all($key,$order_by,$limit,$offset);
  }
  public function get_single_column(array $key,$sorting_order=self::DESC,$limit=null,$offset=null){
    return $this->relation_manager->get_single_column($key,$sorting_order,$limit,$offset);
  }
  public function get_single_row(array $key){
    return $this->relation_manager->get_single_row($key);
  }
  public function get_multiple_rows(array $keys){
    return $this->relation_manager->get_multiple_rows($keys);
  }
  public function insert(array $key){
    return $this->relation_manager->insert($key);
  }
  public function delete(array $key){
    return $this->relation_manager->delete($key);
  }
  public function get_fields_descriptor(){
    return $this->relation_manager->get_fields_descriptor();
  }
}
?>
