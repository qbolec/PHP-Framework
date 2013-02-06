<?php
class IntFieldType implements IFieldType
{
  public function get_normalizer(){
    return new IntLikeValidator();
  }
  public function get_validator(){
    return new IntValidator();
  }
  public function get_pdo_param_type($value){
    return PDO::PARAM_INT;
  }
  public function get_sort_type(){
    return SORT_NUMERIC;
  }
}
?>
