<?php
abstract class AbstractRelationManager implements IRelationManager
{
  private $fields_descriptor;
  public function __construct(IFieldsDescriptor $fields_descriptor){
    $this->fields_descriptor = $fields_descriptor;
  }
  protected function get_assertions(){
    return Framework::get_instance()->get_assertions();
  }
  public function get_fields_descriptor(){
    return $this->fields_descriptor;
  }
  protected function get_fields_description(){
    return $this->get_fields_descriptor()->get_description();
  }
  public function get_single_row(array $key){
    $found = $this->get_all($key);
    $this->get_assertions()->warn_if(1<count($found));
    return Arrays::grab($found,0);
  }
  //a naive implementation
  public function get_multiple_rows(array $keys){
    $result = array();
    foreach($keys as $key){
      try{
        $result[] =$this->get_single_row($key);
      }catch(IsMissingException $e){
        $result[] = null;
      }
    }
    return $result;
  }
  public function get_single_column(array $key,$sort_direction=self::DESC,$limit=null,$offset=null){
    $sought_columns_names = array_keys(array_diff_key($this->get_fields_description(),$key));
    $this->get_assertions()->halt_unless(count($sought_columns_names)==1);
    $column_name = $sought_columns_names[0];
    $rows = $this->get_all($key,array($column_name=>$sort_direction),$limit,$offset);
    if(empty($rows)){
      return array();
    }else{
      $columns = Arrays::transpose($rows);
      return $columns[$column_name];
    }
  }
  protected function validate_data(array $fields_description, array $data){
    ValidatorFactory::get_instance()->get_persistence_data($fields_description)->must_match($data);
  }
  private function expects_subset_of_keys(array $pattern,array $data){
    $unexpected = array_keys(array_diff_key($data,$pattern));
    if(0<count($unexpected)){
      throw new UnexpectedMemberException($unexpected[0]);
    }
  }
  public function get_count(array $key){
    $fields_description = $this->get_fields_description();
    $this->expects_subset_of_keys($fields_description,$key);

    $key_description = array_intersect_key($fields_description,$key);
    $this->validate_data($key_description,$key);
    return $this->prevalidated_get_count($key_description,$key);
  }
  abstract protected function prevalidated_get_count(array $key_description,array $key);
  public function get_all(array $key=array(),array $order_by=array(),$limit=null,$offset=null){
    $fields_description = $this->get_fields_description();
    $this->expects_subset_of_keys($fields_description,$key);
    $this->expects_subset_of_keys($fields_description,$order_by);
  
    $key_description = array_intersect_key($fields_description,$key);
    $this->validate_data($key_description,$key);
    return $this->prevalidated_get_all($key,$order_by,$limit,$offset,$key_description,$fields_description);
  }
  abstract protected function prevalidated_get_all(array $key,array $order_by,$limit,$offset,array $key_description,array $fields_description);
  public function insert(array $key){
    $fd = $this->get_fields_description();
    $this->validate_data($fd,$key);
    return $this->prevalidated_insert($fd,$key);
  }
  abstract protected function prevalidated_insert(array $fields_description,array $key);
  public function delete(array $key){
    $fields_description = $this->get_fields_description();
    $unexpected = array_keys(array_diff_key($key,$fields_description));
    if(0<count($unexpected)){
      throw new UnexpectedMemberException($unexpected[0]);
    }
    $key_description = array_intersect_key($fields_description,$key);
    $this->validate_data($key_description,$key);
    return $this->prevalidated_delete($key_description,$key);
  }
  abstract protected function prevalidated_delete(array $key_description,array $key);
}
?>
