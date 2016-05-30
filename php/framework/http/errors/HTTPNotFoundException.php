<?php
class HTTPNotFoundException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Not Found',404,$env);
  }
  protected function get_body(IRequest $request){
    if(preg_match('@\btext/html\b@',$request->get_header('Accept'))){
      return Framework::get_instance()->get_templates()->get_by_id('404')->get_text(array(
        'uri' => $request->get_method() . ' ' . $request->get_scheme() . '://' . $request->get_host() . $request->get_uri() . $request->get_query(),
      ));
    }else{
      return parent::get_body($request);
    }
  }
}
?>
