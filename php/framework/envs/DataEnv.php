<?php
class DataEnv implements IDataEnv
{
  private $data;
  public function __construct($data){
    $this->data = $data;
  }
  public function get_data(){
    return $this->data;
  }
}
?>
