<?php
class ServerInfoFactoryTest extends FrameworkTestCase
{
  public function testInterface(){
    $sif = new ServerInfoFactory();
    $this->assertInstanceOf('IGetInstance',$sif);
    $this->assertInstanceOf('IServerInfoFactory',$sif);
    $this->assertInstanceOf('IServerInfo',$sif->from_globals());
  }

}
?>
