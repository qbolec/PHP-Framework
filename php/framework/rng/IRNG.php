<?php
interface IRNG
{
  //return random nonnegative integer
  public function next();

  /**
   * @param $seed int (as in mt_srand)
   */ 
  public function set_seed($seed);
}
?>
