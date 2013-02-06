<?php
class Request implements IRequest{
  public $post;//this is public only to display it in log when jsonizing!
  public $get;//this is public only to display it in log when jsonizing!
  private $server;
  private $headers;
  private $body;
  public function __construct($post,$get,$server,$headers,$body){
    $this->post = $post;
    $this->get = $get;
    $this->server = $server;
    $this->headers = $headers;
    $this->body = $body;
  }
  public function get_uri_param($name,$default_value=null){
    return Arrays::get($this->get,$name,$default_value); 
  }
  public function get_post_value($name,$default_value=null){
    return Arrays::get($this->post,$name,$default_value); 
  }
  public function get_host(){
    return Arrays::get($this->server,'SERVER_NAME');
  }
  public function get_port(){
    return Arrays::get($this->server,'SERVER_PORT');
  }
  public function get_path(){
    return Arrays::get($this->server,'SCRIPT_URL','/');
  }
  public function get_uri(){
    return Arrays::get($this->server,'REQUEST_URI');
  }
  public function get_query(){
    return Arrays::get($this->server,'QUERY_STRING','');
  }  
  public function get_scheme(){
    return Arrays::get($this->server,'HTTPS','')=='on'?'https':'http';
  }
  public function get_method(){
    return Arrays::get($this->server,'REQUEST_METHOD','');
  }
  public function is_post(){
    return self::METHOD_POST==$this->get_method();
  }
  public function is_https(){
    return 'https'==$this->get_scheme();
  }
  public function get_header($name,$default_value=null){
    return Arrays::get($this->headers,$name,$default_value);
  }
  public function get_body(){
    return $this->body;
  }
}
?>
