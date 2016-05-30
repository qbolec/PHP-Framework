<?php
class MethodicHandler extends Handler
{
  protected $method_to_interface = array(
    IRequest::METHOD_GET => 'IGetHandler',
    IRequest::METHOD_POST => 'IPostHandler',
    IRequest::METHOD_PUT => 'IPutHandler',
    IRequest::METHOD_DELETE => 'IDeleteHandler',
  );
  private $method_to_function_name = array(
    IRequest::METHOD_GET => 'handle_get',
    IRequest::METHOD_POST => 'handle_post',
    IRequest::METHOD_PUT => 'handle_put',
    IRequest::METHOD_DELETE => 'handle_delete',
  );
  private function get_supported_interfaces(){
    return array_intersect($this->method_to_interface,class_implements($this));
  }
  private function get_handling_function(IRequestEnv $env){
    $method = $env->get_request()->get_method();
    $interfaces = $this->get_supported_interfaces();
    if(array_key_exists($method,$interfaces)){
      return array($this,$this->method_to_function_name[$method]);
    }else{
      throw new HTTPMethodNotAllowedException(array_keys($interfaces),$env);
    }
  }
  public function handle(IRequestEnv $env){
    $handling_function = $this->get_handling_function($env);
    return call_user_func($handling_function,$env);
  }
}
?>
