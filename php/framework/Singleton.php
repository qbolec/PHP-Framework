<?php
class Singleton implements IGetInstance{
  protected static $instances = array();
  protected static function get_default_instance(){
    return new static();
  }
  public static function get_instance(){
    $class_name = get_called_class();
    if(!array_key_exists($class_name,self::$instances)){
      static::set_instance(static::get_default_instance()); 
    }
    return static::$instances[$class_name];
  }
  protected static function set_instance($instance){
    $class_name = get_called_class();
    $previous = array_key_exists($class_name,self::$instances)?self::$instances[$class_name]:null;
    if(null===$instance){
      if(null!==$previous){
        unset(static::$instances[$class_name]);
      }  
    }else{
      static::$instances[$class_name] = $instance;
    }  
    return $previous;
  }
}
?>
