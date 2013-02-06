<?php
interface IResponseFactory
{
  public function from_http_exception(IHTTPException $e);
  public function from_http_body($body);
  public function from_http_headers_and_body(array $headers,$body,$status_code=200,$status_text='OK');
  public function json_from_data($data);
  public function json_from_headers_and_data(array $headers,$data);
}
?>
