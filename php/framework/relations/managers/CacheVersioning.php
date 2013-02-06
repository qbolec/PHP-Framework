<?php
class CacheVersioning implements ICacheVersioning
{
  private $key_prefix;
  private $versions_storage;
  private $key_keys;
  public function __construct(ICache $versions_storage,$key_prefix,array $key_keys){
    $this->key_prefix = $key_prefix;
    $this->versions_storage = $versions_storage;
    $this->key_keys = $key_keys;
  }
  private function encode(array $equalities, array $question_marks){
    $encoded = array_map(array('JSON','encode'),$equalities);
    foreach($question_marks as $key){
      $encoded[$key] = '?';
    }
    ksort($encoded);
    return implode('&',Arrays::map_assoc(function($key,$value){return $key . '=' . $value;},$encoded));
  }
  private function get_version_key_name(array $equalities, array $question_marks){
    return $this->key_prefix . '/version?' . $this->encode($equalities, $question_marks);
  }
  private function get_cache_key($key_name){
    return new CacheKey($this->versions_storage, $key_name);
  }
  private function get_relevant(array $key){
    return array_intersect_key($key,array_flip($this->key_keys));
  }
  //for testing
  protected function get_keys_names_affected_by(array $eqs){
    $question_marks = array_diff($this->key_keys,array_keys($eqs));
    $question_marks_subsets = Arrays::all_subsets($question_marks);
    $eqs_subsets = Arrays::all_subsets($eqs);
    $affected = array();
    foreach($eqs_subsets as $eqs_subset){
      foreach($question_marks_subsets as $question_marks_subset){
        $affected[]= $this->get_version_key_name($eqs_subset,$question_marks_subset);
      }
    }
    return $affected;
  }
  //for testing
  protected function get_keys_names_affecting(array $eqs){
    $affecting = array();
    $eqs_subsets = Arrays::all_subsets($eqs);
    $eqs_keys = array_keys($eqs);
    foreach($eqs_subsets as $eqs_subset){
      $question_marks = array_diff($eqs_keys,array_keys($eqs_subset));
      $affecting[]= $this->get_version_key_name($eqs_subset,$question_marks);
    }
    return $affecting;
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
  public function get_version(array $key){
    list($version) = $this->get_versions(array($key));
    return $version;
  }

  private function get_version_from_revisions(array $revisions,array $affecting_keys_names,array $key, array &$created_revisions){
    $missing_revisions_names = array_keys(array_diff_key(array_flip($affecting_keys_names),$revisions));
    if(!empty($missing_revisions_names)){
      $unique_monotone_value = Framework::get_instance()->get_time();
      $rc = false;
      foreach($missing_revisions_names as $missing_revision_name){
        $missing_cache_key = $this->get_cache_key($missing_revision_name);
        if($missing_cache_key->add($unique_monotone_value)){
          $created_revisions[$missing_revision_name] = 
            $revisions[$missing_revision_name] = $unique_monotone_value;
        }else{
          $rc = true;
        }
      }
      if($rc){
        //R.C. try again.
        //we do not return $unique_monotone_value, as it could be larger, than the value set by another process.
        //In such case, if someone then increments the version, our outdated results would be considered fresh.
        return $this->get_version($key);
      }
    }
    $affecting_revisions = array_intersect_key($revisions,array_flip($affecting_keys_names));
    return $this->combine_revisions_to_version($affecting_revisions);
  }
  private function combine_revisions_to_version(array $revisions){
    ksort($revisions);
    return implode('.',$revisions);
  }
  public function get_versions(array $keys){
    $relevant_parts = array();
    foreach($keys as $key){
      $relevant_parts[] = $this->get_relevant($key);
    }
    return $this->get_prevalidated_versions($relevant_parts);
  }
  private function get_prevalidated_versions(array $keys){
    $result = array();
    $all_key_names = array();
    foreach($keys as $i => $key){
      $all_key_names[$i] = $this->get_keys_names_affecting($key);
    }
    $unique_keys_names = array_values(array_unique(Arrays::flatten($all_key_names)));
    $found = $this->versions_storage->multi_get($unique_keys_names);

    foreach($all_key_names as $i => $key_names){
      $created_revisions = array();
      $version = $this->get_version_from_revisions($found, $key_names, $keys[$i], $created_revisions);
      foreach($created_revisions as $key => $revision){
        $found[$key] = $revision;
      }
      $result[] = $version;
    }
    return $result;
  }
  public function invalidate(array $key){
    $key = $this->get_relevant($key);
    foreach($this->get_keys_names_affected_by($key) as $key_name){
      try{
        $this->get_cache_key($key_name)->increment(1);
      }catch(IsMissingException $miss){
        //jeśli go nie ma, to gdy sie pojawi będzie na pewno inny niż był kiedykolwiek wcześniej
      }
    }
  }
}
?>
