<?php
class PDORelationManager extends AbstractRelationManager
{
  private $pdo_name;
  private $table_name;
  private $sharding;
  public function __construct(IFieldsDescriptor $fields_descriptor,$pdo_name,$table_name,ISharding $sharding){
    parent::__construct($fields_descriptor);
    $this->pdo_name = $pdo_name;
    $this->table_name = $table_name;
    $this->sharding = $sharding;
  }
  private function get_all_shards(){
    $shards_count = $this->get_shards_count();
    $pdos = array();
    for($shard_id=0;$shard_id<$shards_count;++$shard_id){
      $pdos[]=$this->get_pdo_by_shard_id($shard_id);
    }
    return $pdos;
  }
  private function get_pdo_factory(){
    return Framework::get_instance()->get_pdo_factory();
  }
  private function get_shards_count(){
    return $this->get_pdo_factory()->get_shards_count($this->pdo_name);
  }
  private function get_shard_id_from_entity_id($id){
    return $this->sharding->get_shard_id_from_entity_id($this->get_shards_count(),$id);
  }
  private function get_shard_id_from_data_without_id(array $data){
    return $this->sharding->get_shard_id_from_data_without_id($this->get_shards_count(),$data);
  }
  private function get_pdo_by_shard_id($shard_id){
    return $this->get_pdo_factory()->get_pdo($this->pdo_name,$shard_id);
  }
  private function get_pdos(array $key){
    $shard_id = null;
    try{
      $shard_id = $this->get_shard_id_from_data_without_id($key);
    }catch(IsMissingException $e){
      if(array_key_exists('id',$key)){
        $shard_id = $this->sharding->get_shard_id_from_entity_id($key,'id');
      }
    }
    if($shard_id!==null){
      return array($this->get_pdo_by_shard_id($shard_id));
    }else{
      return $this->get_all_shards();
    }
  }
  protected function prevalidated_get_count(array $key_description,array $key){
    $count = 0;
    foreach($this->get_pdos($key) as $pdo){
      $sql = $this->build_select_from_where('count(1) as count',$key,$pdo->strong_equality_operator());
      $q = $pdo->prepare($sql);
      $q->bindValues($key_description,$key);
      $q->execute();
      $count += (int)Arrays::grab($q->fetch(),'count');
    }
    return $count;
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
    $normalized = array();
    //TODO: for a sharded database, you might want to define what does LIMIT, OFFSET and ORDER really mean...
    $pdos = $this->get_pdos($key);
    Framework::get_instance()->get_assertions()->halt_if(count($pdos)>1 && $offset!==null);
    foreach($pdos as $pdo){
      $sql = $this->build_select_from_where($select_part,$key,$pdo->strong_equality_operator());
      if(0!=count($order_by)){
        $orders = array();
        foreach($order_by as $column_name => $order){
          $orders[] = $pdo->order_by_clause($column_name,$order);
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
      foreach($found as $row){
        $normalized_row = array();
        if(0<count($sought)){
          foreach($row as $column_name=>$value){
            $normalized_row[$column_name] =Arrays::grab($fields_description,$column_name)->get_normalizer()->normalize($value);
          }
        }
        $normalized[] = $normalized_row;
      }
    }
    if(count($pdos)>1){
      //tworzymy tymczasową tabelkę z wynikami bo tak najłatwiej nam będzie ją posortować i zaaplikować limit
      $manager = Framework::get_instance()->get_relation_manager_factory()->get_array(FieldsDescriptorFactory::get_instance()->get_from_array($fields_description),$normalized,array());
      return $manager->get_all(array(),$order_by,$limit,$offset);
    }
    return $normalized;
  }
  protected function prevalidated_insert(array $fields_description,array $key){
    $pdos =$this->get_pdos($key);
    Framework::get_instance()->get_assertions()->halt_unless(count($pdos)==1);
    $pdo = $pdos[0];
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
    $count = 0;
    foreach($this->get_pdos($key) as $pdo){
      $sql =
        'DELETE FROM `' . $this->table_name . '` '.
        'WHERE ' . SQLHelper::fields_match_placeholders($key,$pdo->strong_equality_operator());
      $q=$pdo->prepare($sql);
      $q->bindValues($key_description,$key);
      $q->execute();
      $count += $q->rowCount();
    }
    return $count;
  }
}
?>
