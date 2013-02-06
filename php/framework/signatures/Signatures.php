<?php
class Signatures extends MultiInstance implements ISignatures
{
  public function sign($msg){
    $secret = $this->get_secret();
    return hash_hmac('sha1',$msg,$secret);
  }
  private function get_secret(){
    return Config::get_instance()->get('signatures/secret');
  }
}
?>
