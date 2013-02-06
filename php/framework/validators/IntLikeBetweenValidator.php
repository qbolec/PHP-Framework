<?php
class IntLikeBetweenValidator extends AndValidator 
{
  public function __construct($begin = null,$end = null){
    parent::__construct(array(
      new IntLikeValidator(),
      new BetweenValidator($begin, $end),
    ),true);//halt on first error
  }
}
?>
