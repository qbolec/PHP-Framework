<?php
class OptionalNormalizer implements INormalizer
{
  private $inner;
  public function __construct(INormalizer $inner){
    $this->inner = $inner;
  }
  public function normalize($data){
    if(null===$data){
      return null;
    }else{
      return $this->inner->normalize($data);
    }
  }
}
?>
