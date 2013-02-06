<?php
class ValueOutOfRangeException extends SimpleValidationException
{
  public function __construct($value,$begin,$end){
    parent::__construct($value . ' is not in the range [' . $begin . ',' . $end . ')');
  }
}
?>
