<?php
interface IOutput
{
  public function send_status($code,$text);
  public function send_header($header_key,$header_value);
  public function send_body($body);
}
?>
