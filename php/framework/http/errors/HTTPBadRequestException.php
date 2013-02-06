<?php
class HTTPBadRequestException extends HTTPException
{
  private $explainer;
  public function __construct(IValidationExceptionExplainer $explainer,IValidationException $validation_exception,IRequestEnv $env){
    $this->explainer = $explainer;
    parent::__construct('Bad Request',400,$env,$validation_exception);
  }
  protected function get_explanation(){
    return $this->explainer->explain($this->getPrevious());
  }
  protected function get_body(){
    return parent::get_body() . ':<br>' . Convert::to_html($this->get_explanation());
  }
}
?>
