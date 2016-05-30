<?php
class ResponseFactory extends MultiInstance implements IResponseFactory
{
  public function from_http_exception(IHTTPException $e,IRequest $request){
    return $e->get_response($this,$request);
  }
  public function get_redirect($url){
    $body = 'found';
    $headers = array(
      'Location' => $url,
    );
    return $this->from_http_headers_and_body($headers,$body,302,'Found');
  }
  public function from_http_headers_and_body(array $headers,$body,$code=200,$text='OK'){
    return new Response($headers,$body,$code,$text);
  }
  public function from_http_body($body){
    return $this->from_http_headers_and_body(array(),$body,200,'OK');
  }
  public function json_from_data($data){
    return new JSONResponse(array(),$data,200,'OK');
  }
  public function json_from_headers_and_data(array $headers,$data){
    return new JSONResponse($headers,$data,200,'OK');
  }
}
?>
