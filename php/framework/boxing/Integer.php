<?php
class Integer implements IInteger
{
  private $data;
  public function __construct($num){
    if(!is_int($num)){
      throw new CouldNotConvertException($num);
    }
    $this->data = $num;
  }
  public function to_int(){
    return $this->data;
  }
}
?>
