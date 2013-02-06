<?php
class ServerInfoFactory extends MultiInstance implements IServerInfoFactory
{
  public function from_globals(){
    return new ServerInfo($_SERVER);
  }
}
?>
