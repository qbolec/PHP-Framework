<?php
class PDORelationManager extends AbstractRelationManager
{
  private $pdo_name;
  private $table_name;
  private $sharding;
  private $order_to_sql = array(
    self::ASC => 'ASC',
    self::DESC => 'DESC',
  );
  public function __construct(IFieldsDescriptor $fields_descriptor,$pdo_name,$table_name,ISharding $sharding){
    parent::__construct($fields_descriptor);
    $this->pdo_name = $pdo_name;
    $this->table_name = $table_name;
    $this->sharding = $sharding;
  }
  private function get_pdo_factory(){
    return Framework::get_instance()->get_pdo_factory();
  }
  private function get_shards_count(){
    return $this->get_pdo_factory()->get_shards_count($this->pdo_name);
  }
  private function get_shard_id_from_data_without_id(array $data){
    return $this->sharding->get_shard_id_from_data_without_id($this->get_shards_count(),$data);
  }
  private function get_pdo_by_shard_id($shard_id){
    return $this->get_pdo_factory()->get_pdo($this->pdo_name,$shard_id);
  }
  private function get_pdo(array $key){
    return $this->get_pdo_by_shard_id($this->get_shard_id_from_data_without_id($key));
  }
  protected function prevalidated_get_count(array $key_description,array $key){
    $pdo = $this->get_pdo($key);
    $sql = $this->build_select_from_where('count(1) as count',$key,$pdo->strong_equality_operator()); 
    $q = $pdo->prepare($sql);
    $q->bindValues($key_description,$key);
    $q->execute();
    return (int)Arrays::grab($q->fetch(),'count');
  }
  private function build_select_from_where($select_part,array $key, $strong_equality_operator){
    $sql = 
      'SELECT ' . $select_part . ' '.
      'FROM `' . $this->table_name . '` ';
    if(0!=count($key)){
      $sql .= 
        //@xxx: copy&paste SimplePDOPErsistenceManager
        'WHERE ' . SQLHelper::fields_match_placeholders($key, $strong_equality_operator) . ' ';
    }
    return $sql; 
  }
  protected function prevalidated_get_all(array $key,array $order_by,$limit,$offset,array $key_description,array $fields_description){
    $sought = array_diff_key($fields_description,$key);
    if(0==count($sought)){
      $select_part = '1';
    }else{
      $select_part = SQLHelper::fields($sought);
    }
    $pdo = $this->get_pdo($key);
    $sql = $this->build_select_from_where($select_part,$key,$pdo->strong_equality_operator()); 
    if(0!=count($order_by)){
      $orders = array();
      foreach($order_by as $column_name => $order){
        $orders[] = "`$column_name` " . Arrays::grab($this->order_to_sql,$order);  
      }
      $sql .=
        'ORDER BY ' . implode(',',$orders) . ' ';
    }
    if(null!==$limit || null!==$offset){
      $sql .=
        'LIMIT :__limit ';
    }
    if(null!==$offset){
      $sql .=
        'OFFSET :__offset ';
    }
    $q = $pdo->prepare($sql);
    $q->bindValues($key_description,$key);
    if(null!==$limit || null!==$offset){
      $q->bindValue(':__limit',null===$limit?2000000000:$limit,PDO::PARAM_INT);
    }
    if(null!==$offset){
      $q->bindValue(':__offset',$offset,PDO::PARAM_INT);
    }
    $q->execute();
    $found = $q->fetchAll();
    $normalized = array();
    foreach($found as $row){
      $normalized_row = array();
      if(0<count($sought)){
        foreach($row as $key=>$value){
          $normalized_row[$key] =Arrays::grab($fields_description,$key)->get_normalizer()->normalize($value);
        }
      }
      $normalized[] = $normalized_row;
    }
    return $normalized;
  }
  protected function prevalidated_insert(array $fields_description,array $key){
    $pdo =$this->get_pdo($key); 
    $sql = 
      $pdo->insert_ignore_command(). ' INTO `' . $this->table_name . '` '.
      '(' . SQLHelper::fields($key) . ') '.
      'VALUES (' . SQLHelper::placeholders($key) . ')';
    $q=$pdo->prepare($sql);
    $q->bindValues($fields_description,$key);
    $q->execute();
    return 1==$q->rowCount();
  }
  protected function prevalidated_delete(array $key_description,array $key){
    $pdo =$this->get_pdo($key); 
    $sql = 
      'DELETE FROM `' . $this->table_name . '` '.
      'WHERE ' . SQLHelper::fields_match_placeholders($key,$pdo->strong_equality_operator());
    $q=$pdo->prepare($sql);
    $q->bindValues($key_description,$key);
    $q->execute();
    return $q->rowCount();
  }
}
?>
