<?php
class MailComposers extends MultiInstance
{
  public function get_composer(){
    return new MailComposer();
  }
}
?>
