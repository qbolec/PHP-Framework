<?php
interface IMails extends IGetInstance
{
  public function get_mail($to=array(),$subject='',$message='',$headers=array());
}
?>
