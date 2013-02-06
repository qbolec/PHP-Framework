<?php
class ArrayRelationManager extends AbstractRelationManager
{
  private $data;
  private $unique_keys;
  private $direction_to_flag = array(
    IRelationManager::DESC => SORT_DESC,
    IRelationManager::ASC => SORT_ASC,
  );
  public function __construct(IFieldsDescriptor $fields_descriptor,array $data,array $unique_keys){
    parent::__construct($fields_descriptor);
    $this->data = $data;
    $this->unique_keys = $unique_keys;
  }
  private function match(array $row,array $key){
    return count(array_intersect_assoc($row,$key))==count($key);
  }
  public function prevalidated_get_count(array $key_description,array $key){
    $cnt = 0;
    foreach($this->data as $row){
      if($this->match($row,$key)){
        ++$cnt;
      }
    }
    return $cnt;  
  }
  protected function prevalidated_get_all(array $key,array $order_by,$limit,$offset,array $key_description,array $fields_description){
    $found = array();
    foreach($this->data as $row){
      if($this->match($row,$key)){
        $found[]=$row;
      }
    }
    if(!empty($order_by)&&!empty($found)){
      $transposed = Arrays::transpose($found);
      $sort_args = array();
      $fresh_vars = array();
      $i = 0;
      foreach($order_by as $column_name => $direction){
        //@see: https://bugs.php.net/bug.php?id=49353
        //@see: https://bugs.php.net/bug.php?id=49069&edit=3
        $fresh_var[$i] = $transposed[$column_name];
        $fresh_var[$i+1] = $fields_description[$column_name]->get_sort_type();
        $fresh_var[$i+2] = Arrays::grab($this->direction_to_flag,$direction);
    
        $sort_args[]= &$fresh_var[$i];
        $sort_args[]= &$fresh_var[$i+1];
        $sort_args[]= &$fresh_var[$i+2];
        
        $i+=3;
      }
      $sort_args[]=&$found;
      call_user_func_array('array_multisort',$sort_args);
    }
    if(null!==$offset){
      $found = array_slice($found,$offset);
    }
    if(null!==$limit){
      $found = array_slice($found,0,$limit);
    }
    $result = array();
    foreach($found as $row){
      $result[]= array_diff_key($row,$key);
    }
    return $result;
  }
  protected function prevalidated_insert(array $fields_description,array $key){
    foreach($this->unique_keys as $unique_key){
      $restricted_key = array_intersect_key($key,array_flip($unique_key));
      foreach($this->data as $row){
        if($this->match($row,$restricted_key)){
          return false;
        }
      }
    }
    $this->data[]=$key;
    return true;
  }
  protected function prevalidated_delete(array $key_description,array $key){
    $new_data = array();
    $deleted_cnt = 0;
    foreach($this->data as $row){
      if($this->match($row,$key)){
        ++$deleted_cnt;
      }else{
        $new_data[]=$row;
      }
    }
    $this->data = $new_data;
    return $deleted_cnt;
  }
}
?>
