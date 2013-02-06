<?php
//ta klasa jest statyczna, bo jest bezstanowa i nie ma dependencji
//nie ma też żadnego większego sensu ją mockować
class Arrays{
  public static function get(array $arr,$key,$default_value=null){
    return array_key_exists($key,$arr)?$arr[$key]:$default_value;
  }
  public static function grab(array $arr,$key){
    if(!array_key_exists($key,$arr)){
      throw new IsMissingException($key);
    }
    return $arr[$key];
  }
  /**
   * @param $a map
   * @param $b map
   * @return map with keys = keys(a) u keys(b), and conflicting values are taken from $b
   */
  public static function merge(array $a,array $b){
    return $b + $a;
  }
  /**
   * @param $a array (with numeric keys)
   * @param $b array (with numeric keys)
   * @return array with numeric keys in which a is concatenated with b
   */
  public static function concat(array $a,array $b){
    return array_merge($a,$b);
  }
  /**
   * @param $a array (keys will be lost)
   * @param $b array (keys will be lost)
   * @return array which values are set-theoretic union of values of $a and $b 
   */ 
  public static function union(array $a,array $b){
    return self::concat($a,array_diff($b,$a));
  }

  public static function transpose(array $a){
    $transposed = array();
    foreach($a as $row_id => $row){
      foreach($row as $column_id => $cell){
        $transposed[$column_id][$row_id] = $cell;
      }
    }
    return $transposed;
  }
  /**
   * @param $callback key x value -> new_value
   * @param $tab map<key,value>
   * @return map<key,new_value>
   */
  public static function map_assoc($callback,array $tab){
    return empty($tab)?
      array():
      array_combine(array_keys($tab),array_map($callback,array_keys($tab),array_values($tab)));
  }
  public static function combine(array $keys,array $values){
    Framework::get_instance()->get_assertions()->halt_unless(count($keys)==count($values));
    return empty($keys)?array():array_combine($keys,$values);
  }
  /**
   * @param $map map<key,value>
   * @return array<map<key,value> > 
   */
  public static function all_subsets(array $map){
    $result = array(array());
    $copy = $map;
    foreach($map as $key=>$value){
      unset($copy[$key]);
      $with = self::all_subsets($copy);
      foreach($with as &$arr){
        $arr[$key]=$value;
      }
      $result=self::concat($result,$with); 
    }
    return $result;
  }
  public static function insert(array $a,$index,$element){
    $b = $a;
    array_splice($b,$index,0,$element);
    return $b;
  }
  public static function flatten(array $arrs){
    $res = array();
    foreach($arrs as $arr){
      //Do not use:
      //$res = self::concat($res,$arr);
      //as it would make the algorithm Omega(N^2)
      foreach($arr as $el){
        $res[] = $el;
      }
    }
    return $res;
  }
  public static function inflate(array $x){
    $xs = array();
    foreach($x as $i => $a){
      $xs[$i]=array($a);
    }
    return $xs;
  }
  public static function inner_join(array $a_to_b,array $b_to_c){
    $a_to_c = array();
    foreach($a_to_b as $a=>$b){
      if(array_key_exists($b,$b_to_c)){
        $a_to_c[$a]=$b_to_c[$b];
      }
    }
    return $a_to_c;
  }
  //Uwaga! Kolejność par klucz wartość w wynikowej tablicy jest niezdefiniowana.
  public static function intersect_key(array $a,array $b){
    if(count($a)<count($b)){
      return array_intersect_key($a,$b);
    }else{
      $res = array();
      foreach($b as $key => $_value){
        if(array_key_exists($key,$a)){
          $res[$key] = $a[$key];
        }
      }
      return $res;
    }
  }
  public static function set_keys_order(array $map,array $keys_order){
    $result = array();
    foreach($keys_order as $key){
      if(array_key_exists($key, $map)){
        $result[$key] = $map[$key];
      }
    }
    return $result;
  }
  //More sensible range implementation.
  //[$from,$to) intersected with integers.
  //Stdlib range returns nonempty arrays for $to < $from.
  public static function range($from,$to){
    return $to<=$from ? array() : range($from,$to-1,1);
  }
}
?>
