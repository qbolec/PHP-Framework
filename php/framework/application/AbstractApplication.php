<?php
abstract class AbstractApplication extends MockableSingleton implements IApplication
{
  abstract protected function get_root_router();
  protected function get_initial_env(IRequest $request){
    return new RequestEnv($request);
  }
  public function run(){
    $request = Framework::get_instance()->get_request_factory()->from_globals();
    $response = $this->get_response($request);
    $response->send(Framework::get_instance()->get_output());
  }
  public function get_response(IRequest $request){
    Framework::get_instance()->get_logger()->log($request->get_uri());
    $router = $this->get_root_router();
    $env = $this->get_initial_env($request);
    try{
      $resolution = $router->resolve($env);
      $response = $resolution->get_handler()->handle($resolution->get_env());
    }catch(HTTPException $e){
      $response = Framework::get_instance()->get_response_factory()->from_http_exception($e,$request);
    }
    return $response;
  }
}
?>
