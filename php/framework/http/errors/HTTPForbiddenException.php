<?php
class HTTPForbiddenException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Forbidden',403,$env);
  }
}
?>
