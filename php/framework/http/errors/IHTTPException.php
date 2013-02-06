<?php
interface IHTTPException
{
  public function get_response(IResponseFactory $response_factory);
}
?>
