<?php
class BlobFieldType implements IFieldType
{
  public function get_normalizer(){
    return new BlobValidator();
  }
  public function get_validator(){
    return new BlobValidator();
  }
  public function get_pdo_param_type($value){
    return PDO::PARAM_STR;
  }
  public function get_sort_type(){
    return SORT_LOCALE_STRING;
  }
}
?>
