<?php
class JSONHandler extends MethodicHandler
{
  protected $method_to_interface = array(
    IRequest::METHOD_GET => 'IGetJSONHandler',
    IRequest::METHOD_POST => 'IPostJSONHandler',
    IRequest::METHOD_DELETE => 'IDeleteJSONHandler',
  );
  private function must_match(IValidator $validator,IApplicationEnv $env){
    $data = $env->grab(IApplicationEnv::DATA)->get_data();
    if(!$validator->is_valid($data)){
      $error = $validator->get_error($data);
      throw new HTTPBadJSONRequestException($error,$env);
    }
  }
  private function json_from_data($data){
    return Framework::get_instance()->get_response_factory()->json_from_data($data);
  }
  private function decode_data($encoded,IApplicationEnv $env){
    if(null===$encoded){
      return null;
    }else{
      if(is_string($encoded)){
        try{
          return JSON::decode($encoded);
        }catch(CouldNotConvertException $e){
          throw new HTTPBadJSONRequestException($e,$env);
        }
      }else{
        throw new HTTPBadJSONRequestException(new CouldNotConvertException($encoded),$env);
      }
    }
  }
  private function extend_env($encoded,IApplicationEnv $env){
    $data = $this->decode_data($encoded,$env);
    $env->set(IApplicationEnv::DATA,new DataEnv($data));
    return $env;
  }
  protected function handle_get_data(IApplicationEnv $env){
    return $this->json_from_data($this->get_get_data($env));
  }
  public function handle_get(IRequestEnv $env){
    $encoded = $env->get_request()->get_uri_param('data');
    $data_env = $this->extend_env($encoded,$env);
    $validator = $this->get_get_validator($data_env);
    $this->must_match($validator,$data_env);
    return $this->handle_get_data($data_env);
  }
  protected function handle_post_data(IApplicationEnv $env){
    return $this->json_from_data($this->get_post_data($env));
  }
  public function handle_post(IRequestEnv $env){
    $encoded = $env->get_request()->get_post_value('data');
    $data_env = $this->extend_env($encoded,$env);
    $validator = $this->get_post_validator($data_env);
    $this->must_match($validator,$data_env);
    return $this->handle_post_data($data_env);
  }
  protected function handle_delete_data(IApplicationEnv $env){
    return $this->json_from_data($this->get_delete_data($env));
  }
  public function handle_delete(IRequestEnv $env){
    $encoded = $env->get_request()->get_uri_param('data');
    $data_env = $this->extend_env($encoded,$env);
    $validator = $this->get_delete_validator($data_env);
    $this->must_match($validator,$data_env);
    return $this->handle_delete_data($data_env);
  }
}
?>
