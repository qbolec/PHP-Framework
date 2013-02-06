<?php
class RequestEnv extends EmptyEnv implements IRequestEnv
{
  public $request;//this is public only to display it in log when jsonizing!
  public function __construct(IRequest $request){
    $this->request = $request;
  }
  public function get_request(){
    return $this->request;
  }
}
?>
