<?php
interface IRNG
{
  //return random nonnegative integer
  public function next();

  /**
   * @param $from inclusive
   * @param $to exclusive, must be larger than $from
   */
  public function in_range($from,$to);
  /**
   * @param $seed int (as in mt_srand)
   */ 
  public function set_seed($seed);
}
?>
