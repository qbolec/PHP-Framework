<?php
class HTTPPaymentRequiredException extends HTTPException
{
  public function __construct(IRequestEnv $env){
    parent::__construct('Payment Required',402,$env);
  }
}
?>
