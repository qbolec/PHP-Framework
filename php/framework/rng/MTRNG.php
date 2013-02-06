<?php
class MTRNG extends Singleton implements IRNG
{
  public function next(){
    return mt_rand();
  }
  public function set_seed($seed){
    mt_srand($seed);
  }
}
?>
