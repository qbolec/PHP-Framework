<?php
class HTTPException extends Exception implements IHTTPException
{
  public function __construct($status_msg,$status_code,IRequestEnv $env,Exception $previous=null){
    $status_code = Convert::to_int($status_code);
    Framework::get_instance()->get_assertions()->halt_if($status_code<400);
    parent::__construct($status_msg,$status_code,$previous);
  }
  protected function get_headers(){
    return array(
      'Cache-Control' => 'no-cache',//zwłaszcza opensocial, jak mu się nie powie wprost, to keszuje nawet błędy
    );
  }
  protected function get_body(IRequest $request){
    return Convert::to_html($this->getMessage());
  }
  public function get_response(IResponseFactory $response_factory,IRequest $request){
    return $response_factory->from_http_headers_and_body($this->get_headers(),$this->get_body($request),$this->getCode(),$this->getMessage());
  }
}
?>
