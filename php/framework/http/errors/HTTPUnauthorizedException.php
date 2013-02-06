<?php
class HTTPUnauthorizedException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Unauthorized',401,$env);
  }
}
?>
