<?php
class SimplePDOPersistenceManager implements IPersistenceManager
{
  private $fields_descriptor;
  private $pdo_name;
  private $table_name;
  private $sharding;
  public function __construct(IFieldsDescriptor $fields_descriptor,ISharding $sharding,$pdo_name,$table_name){
    $this->fields_descriptor = $fields_descriptor;
    $this->pdo_name = $pdo_name;
    $this->table_name = $table_name;
    $this->sharding = $sharding;
  }
  private function normalize_data(array $fields_description,array $data){
    $normalized_data = array();
    foreach($fields_description as $field_name => $type){
      $normalizer = $type->get_normalizer();
      $normalized_data[$field_name] = $normalizer->normalize(Arrays::grab($data,$field_name));
    }
    return $normalized_data;
  }
  private function get_id_description(){
    return Arrays::grab($this->get_fields_description(),'id');
  }
  private function get_id_validator(){
    return $this->get_id_description()->get_validator();
  }
  private function get_id_pdo_param_type($id){
    return $this->get_id_description()->get_pdo_param_type($id);
  }
  public function get_by_id($id){
    $this->get_id_validator()->must_match($id);
    $fields_description = $this->get_fields_description();
    $sql = 
      'SELECT ' . SQLHelper::fields($fields_description) . ' '.
      'FROM `' . $this->table_name . '` '.
      'WHERE id=:id';
    $q = $this->execute_with_id($sql, $id);
    $data = $q->fetch();
    if(false===$data){
      throw new NoSuchEntityException($id);
    }
    return $this->normalize_data($fields_description,$data);
  }
  private function execute_with_id($sql, $id){
    $pdo = $this->get_pdo_by_id($id);
    $q = $pdo->prepare($sql);
    $q->bindValue(':id',$id,$this->get_id_pdo_param_type($id));
    $q->execute();
    return $q;
  }
  private function execute_with_id_and_data($sql,$id,array $fields_description,array $data){
    $pdo = $this->get_pdo_by_id($id);
    $q = $pdo->prepare($sql);
    $q->bindValue(':id',$id,$this->get_id_pdo_param_type($id));
    $q->bindValues($fields_description,$data);
    $q->execute();
    return $q;
  }
  public function delete_by_id($id){
    $this->get_id_validator()->must_match($id);
    $sql =
      'DELETE FROM `' . $this->table_name . '` '.
      'WHERE id=:id';
    $q = $this->execute_with_id($sql, $id);
    return $q->rowCount() === 1; 
  }
  private function validate_data(array $fields_description, array $data){
    ValidatorFactory::get_instance()->get_persistence_data($fields_description)->must_match($data);
  }
  private function build_insert_sql(array $data,$insert_command){
    return
      $insert_command .' INTO `' . $this->table_name . '` '.
      '(' . SQLHelper::fields($data) . ') VALUES (' . SQLHelper::placeholders($data) . ')';
  }
  private function build_update_sql(array $data){
    return
      'UPDATE `' . $this->table_name . '` '.
      'SET ' . SQLHelper::assign_placeholders_to_fields($data) . ' ' .
      'WHERE id=:id';
  }
  private function prepare_bind_execute(IPDO $pdo,$sql,array $fields_description,array $data){
    $q = $pdo->prepare($sql);
    $q->bindValues($fields_description,$data);
    $q->execute();
    return $q; 
  }
  public function insert_and_assign_id(array $data){
    $fields_description = $this->get_fields_description();
    $data_fields_description = array_diff_key($fields_description,array('id'=>0));
    $this->validate_data($data_fields_description,$data);
    $sql = $this->build_insert_sql($data,'INSERT');
    $pdo = $this->get_pdo_from_data_without_id($data);
    $q = $this->prepare_bind_execute($pdo,$sql,$data_fields_description,$data);
    return $fields_description['id']->get_normalizer()->normalize($pdo->lastInsertId());
  }
  public function insert(array $data){
    $fields_description = $this->get_fields_description();
    $this->validate_data($fields_description,$data);
    $pdo = $this->get_pdo_by_id($data['id']);
    $sql = $this->build_insert_sql($data,$pdo->insert_ignore_command());
    $q = $this->prepare_bind_execute($pdo,$sql,$fields_description,$data);
    return $q->rowCount()==1;
  }
  public function save(array $current_data, array $original_data){
    $id = Arrays::grab($current_data,'id');
    $unexpected = array_keys(array_diff_key($original_data,$current_data));
    if(0<count($unexpected)){
      throw new UnexpectedMemberException($unexpected[0]);
    }
    $fields_description = $this->get_fields_description();
    $this->validate_data($fields_description,$current_data);
    $changed_data = Arrays::diff_assoc(array_intersect_key($current_data,$original_data),$original_data);
    if(array_key_exists('id',$changed_data)){
      throw new UnexpectedMemberException('id');
    }
    if(0!=count($changed_data)){
      $changed_fields_description = array_intersect_key( 
        $fields_description,
        $changed_data
      );
      $sql=$this->build_update_sql($changed_data);
      $q = $this->execute_with_id_and_data($sql,$id,$changed_fields_description,$changed_data);
      return $q->rowCount() === 1; 
    }else{
      return false;
    }
  }
  public function multi_get_by_ids(array $ids){
    $sharded = array();
    $id_validator = $this->get_id_validator();
    foreach($ids as $id){
      $id_validator->must_match($id);
      $shard_id = $this->get_shard_id_by_id($id);
      $sharded[$shard_id][]=$id;
    }
    $fields_description = $this->get_fields_description();
    $sql_select = 
      'SELECT ' . SQLHelper::fields($fields_description) . ' '.
      'FROM `' . $this->table_name . '` ';
    $result = array();
    foreach($sharded as $shard_id => $ids_part){
      $pdo = $this->get_pdo_by_shard_id($shard_id);
      $sql_where = 'WHERE id IN ('.implode(',',$ids_part).')';
      $q = $pdo->prepare($sql_select . $sql_where);
      $q->execute();
      while($data = $q->fetch()){
        $normalized_data = $this->normalize_data($fields_description,$data);
        $result[$normalized_data['id']] = $normalized_data;
      }
    }
    return Arrays::set_keys_order($result,$ids);
  }
  private function get_pdo_by_shard_id($shard_id){
    return $this->get_pdo_factory()->get_pdo($this->pdo_name,$shard_id);
  }
  private function get_pdo_from_data_without_id(array $data){
    return $this->get_pdo_by_shard_id($this->get_shard_id_from_data_without_id($data));
  }
  public function get_fields_descriptor(){
    return $this->fields_descriptor;
  }
  protected function get_fields_description(){
    return $this->get_fields_descriptor()->get_description();
  }
  private function get_pdo_by_id($id){
    return $this->get_pdo_by_shard_id($this->get_shard_id_by_id($id));
  }
  private function get_shard_id_by_id($id){
    return $this->sharding->get_shard_id_from_entity_id($this->get_shards_count(),$id);
  }
  private function get_shard_id_from_data_without_id(array $data){
    return $this->sharding->get_shard_id_from_data_without_id($this->get_shards_count(),$data);
  }
  private function get_shards_count(){
    return $this->get_pdo_factory()->get_shards_count($this->pdo_name);
  }
  protected function get_pdo_factory(){
    return Framework::get_instance()->get_pdo_factory();
  }
}
?>
