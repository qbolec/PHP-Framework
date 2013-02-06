<?php
class IntBetweenValidator extends AndValidator 
{
  public function __construct($begin = null,$end = null){
    parent::__construct(array(
      new IntValidator(),
      new BetweenValidator($begin, $end),
    ),true);//halt on first error
  }
}
?>
