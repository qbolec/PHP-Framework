<?php
class HTTPNotFoundException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Not Found',404,$env);
  }
}
?>
