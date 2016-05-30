<?php
class Mails extends MultiInstance implements IMails
{
  public function get_mail($to=array(),$subject='',$message='',$headers=array()){
    return new Mail($to,$subject,$message,$headers);
  }
}
?>
