<?php
class Response implements IResponse
{
  private $status_code;
  private $status_text;
  private $headers;
  private $body;
  public function __construct(array $headers,$body,$status_code,$status_text){
    $this->headers = $headers;
    $this->body = $body;
    $this->status_code = $status_code;
    $this->status_text = $status_text;
  }
  public function send(IOutput $output){
    $output->send_status($this->status_code,$this->status_text);
    foreach($this->headers as $key => $value){
      $output->send_header($key,$value);
    }
    $output->send_body($this->body);
  }
}
?>
