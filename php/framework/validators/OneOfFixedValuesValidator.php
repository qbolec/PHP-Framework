<?php
class OneOfFixedValuesValidator extends OrValidator
{
  public function __construct(array $values){
    parent::__construct(array_map(function($value){return new FixedValueValidator($value);},$values));
  }
}
?>
