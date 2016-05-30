<?php
class ValueOutOfRangeException extends SimpleValidationException
{
  public function __construct($value,$begin,$end){
    $range = ($begin === null ? '(-∞,' : '[' . $begin . ',' ) . ($end === null ? '∞)' :  $end . ')');
    parent::__construct($value . ' is not in the range ' . $range);
  }
}
?>
