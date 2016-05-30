<?php
class JSONResponse extends Response
{
  public function __construct(array $headers,$data,$status_code,$status_text){
    $json_headers = array(
      'Cache-Control' => 'no-cache',
      'Content-Type' => 'application/json; charset=UTF-8',
    );
    $ex_headers = Arrays::merge($json_headers,$headers);
    $body = JSON::encode($data);
    parent::__construct($ex_headers,$body,$status_code,$status_text);
  }
}
?>
