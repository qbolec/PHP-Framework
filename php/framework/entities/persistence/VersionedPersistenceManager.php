<?php
class VersionedPersistenceManager extends PersistenceManagerWrapper
{
  private $versioning;
  public function __construct(IPersistenceManager $pm,ICacheVersioning $versioning){
    parent::__construct($pm);
    $this->versioning = $versioning;
  }
  public function delete_by_id($id){
    $res = parent::delete_by_id($id);
    if($res){
      //@todo: może jednak ciągnąć brakujące dane przed deletem?
      $this->versioning->invalidate(array('id'=>$id));
    }
    return $res;
  }
  public function insert(array $data){
    $res = parent::insert($data);
    if($res){
      $this->versioning->invalidate($data);
    }  
    return $res;
  }
  public function insert_and_assign_id(array $data_without_id){
    $id = parent::insert_and_assign_id($data_without_id);
    
    $data = $data_without_id;
    $data['id'] = $id;

    $this->versioning->invalidate($data);
    return $id;
  }
  public function save(array $new_data,array $old_data){
    $res = parent::save($new_data,$old_data);
    if($res){
      $this->versioning->invalidate($new_data);
      $this->versioning->invalidate(Arrays::merge($new_data,$old_data));
    }
    return $res;
  }
}
?>
