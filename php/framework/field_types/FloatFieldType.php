<?php
class FloatFieldType implements IFieldType
{
  const TYPE = 'FLOAT';
  public function get_normalizer(){
    return new FloatLikeValidator();
  }
  public function get_validator(){
    return new FloatValidator();
  }
  public function get_pdo_param_type($value){
    //PHP nie zdefiniowało nigdy PARAM_FLOAT :/
    return self::TYPE;
  }
  public function get_sort_type(){
    return SORT_NUMERIC;
  }
}
?>
