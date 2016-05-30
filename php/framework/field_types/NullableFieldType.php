<?php
class NullableFieldType implements IFieldType
{
  private $inner;
  public function __construct(IFieldType $inner){
    $this->inner = $inner;
  }
  public function get_normalizer(){
    return new OptionalNormalizer($this->inner->get_normalizer());
  }
  public function get_validator(){
    return new NullableValidator($this->inner->get_validator());
  }
  public function get_pdo_param_type($value){
    return null===$value? 
      PDO::PARAM_NULL:
      $this->inner->get_pdo_param_type($value);
  }
  public function get_sort_type(){
    return $this->inner->get_sort_type();
  }
}
?>
