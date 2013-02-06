<?php
class HTTPMethodNotAllowedException extends HTTPException
{
  private $allowed_methods;
  public function __construct(array $allowed_methods,IRequestEnv $env){
    $this->allowed_methods = $allowed_methods;
    parent::__construct('Method Not Allowed',405,$env);
  }
  protected function get_headers(){
    return Arrays::merge(
      parent::get_headers(),
      array(
        'Allow'=>  implode(', ',$this->allowed_methods),
      )
    );
  }
}
?>
