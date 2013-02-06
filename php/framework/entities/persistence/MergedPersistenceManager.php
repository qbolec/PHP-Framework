<?php
class MergedPersistenceManager implements IPersistenceManager
{
  private $base;
  private $extension;
  public function __construct(IPersistenceManager $base,IPersistenceManager $extension){
    $this->base = $base;
    $this->extension = $extension;
  }
  public function get_by_id($id){
    $base_data = $this->base->get_by_id($id);
    try{
      $extension_data = $this->extension->get_by_id($id);
    }catch(NoSuchEntityException $e){
      $this->log();
      throw $e;
    }  
    return Arrays::merge($base_data,$extension_data);
  }
  public function delete_by_id($id){
    $ret = $this->base->delete_by_id($id);
    if($ret!=$this->extension->delete_by_id($id)){
      $this->log($ret);
    }
    return $ret;
  }
  public function insert_and_assign_id(array $data_without_id){
    list($base_data_without_id,$extension_data_without_id) = $this->split($data_without_id);
    $id = $this->base->insert_and_assign_id($base_data_without_id);
    $extension_data = array_merge($extension_data_without_id,array(
      'id' => $id,
    ));
    if(!$this->extension->insert($extension_data)){
      $this->log($id);
    }
    return $id;
  }
  public function insert(array $data){
    list($base_data,$extension_data)=$this->split($data);
    if($this->base->insert($base_data)){
      if(!$this->extension->insert($extension_data)){
        $this->log($data['id']);
      }
      return true;
    }else{
      return false;
    }
  }
  private function split(array $data){
    $base_data= array_intersect_key($data, $this->base->get_fields_descriptor()->get_description());
    $extension_data = array_intersect_key($data, $this->extension->get_fields_descriptor()->get_description());
    return array($base_data,$extension_data);
  }
  public function save(array $current_data,array $original_data){
    list($base_current_data,$extension_current_data)=$this->split($current_data);
    list($base_original_data,$extension_original_data)=$this->split($original_data);
    $this->base->save($base_current_data,$base_original_data);
    $this->extension->save($extension_current_data,$extension_original_data);
  }
  public function multi_get_by_ids(array $ids){
    $base_result = $this->base->multi_get_by_ids($ids);
    $extension_result = $this->extension->multi_get_by_ids(array_keys($base_result));
    $full_result = array();
    foreach($extension_result as $id=>$extension_data){
      $full_result[$id] = Arrays::merge($base_result[$id],$extension_data);
    }
    if(count($extension_result)!=count($base_result)){
      $missing_extensions = array_keys(array_diff_key($extension_result,$base_result));
      $this->log($missing_extensions);
    }
    return $full_result;
  }
  protected function log(){
    Framework::get_instance()->get_logger()->log();
  }
  public function get_fields_descriptor(){
    return FieldsDescriptorFactory::get_instance()->get_merged(
      $this->base->get_fields_descriptor(),
      $this->extension->get_fields_descriptor()
    );
  }
}
?>
