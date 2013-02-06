<?php
class HTTPConflictException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Conflict',409,$env);
  }
}
?>
