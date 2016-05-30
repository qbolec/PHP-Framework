<?php
class MTRNG extends Singleton implements IRNG
{
  public function next(){
    return mt_rand();
  }
  public function in_range($from,$to){
    return mt_rand($from,$to-1);
  }
  public function set_seed($seed){
    mt_srand($seed);
  }
}
?>
