<?php
class BetweenValidator extends AbstractValidator
{
  private $begin = null;
  private $end = null;
  public function __construct($begin = null,$end = null){
    $this->begin = $begin;
    $this->end = $end;
  }
  public function get_error($data){
    if(null !== $this->begin && $data<$this->begin || null !== $this->end && $this->end<=$data){
      return new ValueOutOfRangeException($data,$this->begin,$this->end);
    }else{
      return null;
    }
  }
}
?>
