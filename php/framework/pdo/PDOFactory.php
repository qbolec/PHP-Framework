<?php
class PDOFactory extends AbstractConfigurableConnectionsFactory implements IPDOFactory
{
  const TOO_MANY_CONNECTIONS = 1040;
  public function get_pdo($name, $shard_id){
    $path = "pdos/masters/$name/$shard_id";
    return $this->get_connection_for_config_path($path);
  }
  public function get_shards_count($name){
    $path = "pdos/masters/$name";
    return count($this->get_config()->get($path));
  }
  private function get_username_and_password($user){
    $path = "pdos/users/$user";
    return $this->get_config()->get($path);
  }
  private function get_endpoint_info($endpoint){
    $path = "pdos/endpoints/$endpoint";
    return $this->get_config()->get($path);
  }
  protected function spawn(array $info){
    $dsn = Arrays::grab($info,'dsn');
    $endpoint = Arrays::get($info,'endpoint');
    if(null!==$endpoint){
      $endpoint_info = $this->get_endpoint_info($endpoint);
      $host = Arrays::get($endpoint_info,'host');
      if(null!==$host){
        $dsn .= ';host=' . $host;
      }
      $port = Arrays::get($endpoint_info,'port');
      if(null!==$port){
        $dsn .= ';port=' . $port;
      }
    }
    $user = Arrays::get($info,'user');
    if(null!==$user){
      $user_info = $this->get_username_and_password($user);
    }else{
      $user_info = $info;
    }
    $username = Arrays::grab($user_info,'username');
    $password = Arrays::grab($user_info,'password');
    if(preg_match('@^sqlite:@',$dsn)){
      return new PDOSqlite($dsn,$username,$password);
    }else{
      try{
        return new PDOEx($dsn,$username,$password);
      }catch(PDOException $e){
        if(self::TOO_MANY_CONNECTIONS == $e->getCode()){
          //garbage collect PDO objects, which should free connections
          $this->connections = array();
          return new PDOEx($dsn,$username,$password);
        }else{
          throw $e;
        }
      }
    }
  }
}
?>
