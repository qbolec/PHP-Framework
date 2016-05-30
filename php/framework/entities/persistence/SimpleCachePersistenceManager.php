<?php
class SimpleCachePersistenceManager implements IPersistenceManager
{
  private $fields_descriptor;
  private $cache;
  private $key_prefix;
  public function __construct(IFieldsDescriptor $fields_descriptor,IPrefetchingCache $cache,$key_prefix){
    $this->fields_descriptor=$fields_descriptor;
    $this->cache=$cache;
    $this->key_prefix = $key_prefix;
  }
  public function get_by_id($id){
    try{
      $encoded_data = $this->get_cache_key($id)->get();
    }catch(IsMissingException $key){
      throw new NoSuchEntityException($id);
    }
    try{
      return $this->decode_data($encoded_data);
    }catch(IValidationException $e){
      //jeśli nie usuniemy tej wadliwej wersji klucza,
      //to layered cache key będzie próbował robić insert
      //który jest tłumaczony na add() który się nie powiedzie
      $this->delete_by_id($id);
      throw new NoSuchEntityException($id);
    }
  }
  //@todo implode(xFF) + version
  protected function encode_data(array $data){
    $this->validate_data($data);
    return $data;
  }
  private $fields_description;
  private function get_fields_description(){
    if(null === $this->fields_description){
      $this->fields_description = $this->get_fields_descriptor()->get_description();
    }
    return $this->fields_description;
  }
  private function get_id_validator(){
    return Arrays::grab($this->get_fields_description(),'id')->get_validator();
  }
  private function validate_data(array $data){
    $validator = $this->get_fields_descriptor()->get_validator();
    $validator->must_match($data);
  }
  public function get_fields_descriptor(){
    return $this->fields_descriptor;
  }
  //@todo versioncheck+explode(xFF)+datavalidation
  protected function decode_data($encoded_data){
    //@todo czy na pewno jest sens marnować czas na walidację danych z keszu??
    if(!is_array($encoded_data)){
      throw new CouldNotConvertException($encoded_data);
    }
    $this->validate_data($encoded_data);
    return $encoded_data;
  }
  private function get_cache_key_name($id){
    $this->get_id_validator()->must_match($id);
    return $this->key_prefix . '/' . $id;
  }
  private function get_cache_key($id){
    return new CacheKey(
      $this->cache,
      $this->get_cache_key_name($id)
    );
  }
  public function delete_by_id($id){
    return $this->get_cache_key($id)->delete();
  }
  public function insert_and_assign_id(array $data){
    throw new BadMethodCallException('nie mogę Ci pomóc, jestem koniem');
  }
  private function get_id_from_data(array $data){
    return Arrays::grab($data,'id');
  }
  public function insert(array $data){
    $encoded_data = $this->encode_data($data);
    return $this->get_cache_key($this->get_id_from_data($data))->add($encoded_data);
  }
  public function save(array $current_data,array $original_data){
    $encoded_data = $this->encode_data($current_data);
    $id = $this->get_id_from_data($current_data);
    //@todo: remove copy&paste SimplePDOPersistenceManager.php
    $unexpected=array_diff_key($original_data,$current_data);
    if(0<count($unexpected)){
      throw new UnexpectedMemberException(array_pop($unexpected));
    }
    $changed = Arrays::diff_assoc(array_intersect_key($current_data,$original_data),$original_data);
    if(array_key_exists('id',$changed)){
      throw new UnexpectedMemberException('id');
    }
    if(0<count($changed)){
      $this->get_cache_key($id)->set($encoded_data);
    }
  }
  public function multi_get_by_ids(array $ids){
    $cache_keys_names = array();
    foreach($ids as $i => $id){
      $cache_keys_names[$i] = $this->get_cache_key_name($id);
    }
    $found_encoded = $this->cache->multi_get($cache_keys_names);
    $result = array();
    foreach($ids as $i => $id){
      $cache_key_name = $cache_keys_names[$i];
      if(array_key_exists($cache_key_name,$found_encoded)){
        try{
          $result[$id] = $this->decode_data($found_encoded[$cache_key_name]);
        }catch(IValidationException $e){
          $this->delete_by_id($id);
        }
      }

    }
    return $result;
  }
}
?>
