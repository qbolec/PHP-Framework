<?php
function my_assert($b){
  if(!$b){
    throw new LogicException();
  }
}
my_assert(true);
?>
