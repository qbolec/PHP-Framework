<?php
class IsPermuationOf extends PHPUnit_Framework_Constraint
{
  private $correct;
  public function __construct(array $correct){
    $this->correct = $correct;
  }
  public function array_is_subset_of($xs,$ys){
    //nie możemy tu użyć array_diff, ani sort, bo obie funkcje konwertują do stringów!
    foreach($xs as $x){
      $index=array_search($x,$ys,true);
      if(FALSE===$index){
        return false;
      }else{
        unset($ys[$index]);
      }
    } 
    return true;
  }
  public function evaluate($other){
    if(!is_array($other)){
      throw new InvalidArgumentException();
    }
    $xs = $this->correct;
    $ys = $other;
    return $this->array_is_subset_of($xs,$ys)&&$this->array_is_subset_of($ys,$xs);
  }
  public function toString(){
    return 'is permuation of ' . JSON::encode($this->correct);
  }
}
?>
