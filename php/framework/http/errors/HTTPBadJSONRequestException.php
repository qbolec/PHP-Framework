<?php
class HTTPBadJSONRequestException extends HTTPBadRequestException
{
  public function __construct(IValidationException $exception,IRequestEnv $env){
    parent::__construct(Framework::get_instance()->get_validation_exception_explainer_factory()->get_json(),$exception,$env);
  }
  protected function get_headers(){
    return Arrays::merge(
      parent::get_headers(),
      array(
        'Content-Type'=> 'application/json; charset=UTF-8',
      )
    );
  }
  protected function get_body(IRequest $request){
    return $this->get_explanation();
  }
}
?>
