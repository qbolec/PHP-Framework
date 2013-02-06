<?php
class ServerInfo implements IServerInfo
{
  private $info;
  public function __construct(array $info){
    $this->info = $info;
  }
  public function get_user(){
    return Arrays::get($this->info,'USER');
  }
  public function get_host(){
    $host = Arrays::get($this->info,'SERVER_NAME');
    if(null===$host){
      return gethostname();//syscall?
    }else{
      return $host;
    }
  }
}
?>
