<?php
class HTTPInternalServerErrorException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Internal Server Error',500,$env);
  }
}
?>
