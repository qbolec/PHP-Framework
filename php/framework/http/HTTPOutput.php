<?php
class HTTPOutput extends Singleton implements IOutput
{
  //ze względu na headers_sent, chcę bardzo by był to singleton
  private $headers_sent = false;
  public function send_status($code,$text){
    $this->raw_header('HTTP/1.1 ' . $code . ' ' . $text);
  }
  public function send_header($header_key,$header_value){
    $this->raw_header($header_key . ': ' . $header_value);
  }
  protected function raw_header($header){
    Framework::get_instance()->get_assertions()->halt_if($this->headers_sent);
    header($header);
  }
  public function send_body($body){
    $this->headers_sent=true;
    echo $body;
  }
}
?>
