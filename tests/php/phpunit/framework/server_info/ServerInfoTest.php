<?php
class ServerInfoTest extends FrameworkTestCase
{
  public function testInterface(){
    $si = new ServerInfo(array());
    $this->assertInstanceOf('IServerInfo',$si);
  }
  public function testGetUser(){
    $user = 'xxx';
    $si = new ServerInfo(array('USER'=>$user));
    $this->assertSame($user,$si->get_user());
  }
  public function testGetHostFromServerArray(){
    $server_name = 'gruszka.pl';
    $si = new ServerInfo(array('SERVER_NAME'=>$server_name));
    $this->assertSame($server_name,$si->get_host());
  }
  public function testGetHostFromSystemCall(){
    $server_name = gethostname();
    $si = new ServerInfo(array());
    $this->assertSame($server_name,$si->get_host()); 
  }
}
?>
