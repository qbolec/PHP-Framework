<?php
class CachedRelationManager extends RelationManagerWrapper
{
  private $cache;
  private $key_prefix;
  private $versioning;
  public function __construct(IPrefetchingCache $cache,ICacheVersioning $versioning,$key_prefix,IRelationManager $relation_manager){
    parent::__construct($relation_manager);
    $this->cache = $cache;
    $this->key_prefix = $key_prefix;
    $this->versioning = $versioning;
  }
  private function get_description(){
    return $this->get_fields_descriptor()->get_description();
  }
  private function encode(array $key){
    return JSON::encode($key);
  }
  //important: this function should be called BEFORE actual SELECT is done
  //so that in case another process updates relation AFTER the SELECT and increments the version, the result of SELECT will be stored under outdated key.
  //For the very same reason, the update of relation should be done BEFORE the increment of the version.
  //Proces A performs:
  //A1 : get version
  //A2 : get cached query $version
  //A3 : SELECT
  //A4 : set cached query $version
  //Proces B performs:
  //B1 : UPDATE/DELETE/INSERT
  //B2 : increment version
  //When both proceses finish, we for sure have :
  //1. $version got incremented by 1
  //2. if there is cached query $version, the the SELECT was performed after UPDATE, so data is fresh and correct
  private function get_version(array $key){
    return $this->versioning->get_version($key);
  }
  private function get_cached_query_key($function_name,array $args){
    $key = $args[0];
    $this->validate_key($key);  
    $version = $this->get_version($key);
    return new CacheKey($this->cache,$this->key_prefix . '/' . $version . '/' . $function_name . '?' . $this->encode($args));  
  }
  private function get_cached_query($function_name,array $args){
    $cached_query_key = $this->get_cached_query_key($function_name,$args);
    try{
      return $cached_query_key->get();
    }catch(IsMissingException $e){
      $result=call_user_func_array(array($this,"parent::$function_name"),$args);
      $cached_query_key->set($result);
      return $result;
    }
  }
  public function get_count(array $key){
    return $this->get_cached_query(__FUNCTION__,func_get_args());
  }
  public function get_all(array $key,array $order_by=array(),$limit=null,$offset=null){
    return $this->get_cached_query(__FUNCTION__,func_get_args());
  }
  public function get_single_column(array $key,$sorting_order=self::DESC,$limit=null,$offset=null){
    return $this->get_cached_query(__FUNCTION__,func_get_args());
  }
  public function get_single_row(array $key){
    return $this->get_cached_query(__FUNCTION__,func_get_args());
  }
  //hrdocRE1!
  public function get_multiple_rows(array $keys){
    if(empty($keys)){
      return array();//zazwyczaj tak nie robię, ale nie mogłem się oprzeć
    }
    $assertions = Framework::get_instance()->get_assertions();
    foreach($keys as $key){
      $assertions->halt_unless(array_keys($key) == array_keys($keys[0]));
    }
    $this->validate_key($keys[0]);
    $versions = $this->versioning->get_versions($keys);
    $cache_keys = array();
    $function_name = 'get_single_row';
    foreach($keys as $i => $key){
      $version = $versions[$i];
      $cache_keys[] = $this->key_prefix . '/' . $version . '/' . $function_name . '?' . $this->encode(array($key));
    }
    $cached = $this->cache->multi_get($cache_keys);
    $missing_keys = array();
    //zależy mi na tym by kolejność w missing_keys była deterministyczna
    //więc nawet nie będę próbował z jakimiś array_diff_key itp.
    foreach($cache_keys as $idx => $cache_key){
      if(!array_key_exists($cache_key, $cached)){
        $missing_keys[] = $keys[$idx];
      }
    }
    if(!empty($missing_keys)){
      $found = parent::get_multiple_rows($missing_keys);
    }
    $result = array();
    $f = 0;
    foreach($cache_keys as $idx => $cache_key){
      if(array_key_exists($cache_key, $cached)){
        $result[] = $cached[$cache_key];
      }else{//jeśli klucz nie istniał w cached, to trafił do missing_keys a potem do found
        $value = $found[$f++];
        if(null!==$value){//jeśli mówią, że go nie ma to nic nie rób, wpp. dodaj do cacheu
          $this->cache->set($cache_key, $value);
        }
        //niezależnie czy był hit czy miss, zwróc jakiś wynik
        $result[] = $value;
      }
    }
    return $result;
  }
  private function perform_change($function_name,array $key){
    $result = parent::$function_name($key);
    if($result){//trochę brzydko, bo czasem chodzi o bool (insert) a czasem o int (delete)
      $this->invalidate_cache($key);
    }
    return $result; 
  }
  //@TODO: copy&paste z AbstractRelationManager
  protected function validate_data(array $fields_description, array $data){
    ValidatorFactory::get_instance()->get_persistence_data($fields_description)->must_match($data);
  }
  private function validate_key(array $key){
    $key_description = array_intersect_key($this->get_description(),$key);
    return $this->validate_data($key_description,$key); 
  }
  private function invalidate_cache(array $key){
    $this->versioning->invalidate($key);
  }
  public function insert(array $key){
    return $this->perform_change(__FUNCTION__,$key);
  }
  public function delete(array $key){
    return $this->perform_change(__FUNCTION__,$key);
  }
}
?>
