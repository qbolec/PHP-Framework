<?php
class LoggerTest extends FrameworkTestCase{
  public function testLogAddsToLog(){
    $this->setServerInfo();
    $log_writer = $this->getMock('ILogWriter');
    $log_writer
      ->expects($this->once())
      ->method('describe')
      ->with($this->anything(),$this->equalTo('akuku'))
      ->will($this->returnValue('Akuku'));
    $writer_factory = $this->getMock('ILogWriterFactory');
    $writer_factory
      ->expects($this->once())
      ->method('get_by_verbosity')
      ->with($this->equalTo(42))
      ->will($this->returnValue($log_writer));
    $logger = $this->getMock('Logger',array('classify','add_text','get_log_writer_factory'));
    $logger 
      ->expects($this->once())
      ->method('classify')
      ->will($this->returnValue(array(
        'verbosity'=>42,
        'priority'=>LOG_WARNING
      )));
    $logger
      ->expects($this->once())
      ->method('get_log_writer_factory')
      ->will($this->returnValue($writer_factory));
    $logger
      ->expects($this->once())
      ->method('add_text')
      ->with(LOG_WARNING,LOG_LOCAL0,'test@testing','Akuku');

    $logger->log('akuku');
  }
  public function testUsesRequestHostNameAsIdent(){
    $host = 'testing.vanisoft.pl';
    $request = $this->getMock('IRequest');
    $request
      ->expects($this->atLeastOnce())
      ->method('get_host')
      ->will($this->returnValue($host));
    $request_factory = $this->getMock('IRequestFactory');
    $request_factory
      ->expects($this->once())
      ->method('from_globals')
      ->will($this->returnValue($request));
    $framework = $this->getMock('Framework',array('get_server_info_factory','get_request_factory'));
    $framework
      ->expects($this->never())
      ->method('get_server_info_factory');
    $framework
      ->expects($this->once())
      ->method('get_request_factory')
      ->will($this->returnValue($request_factory));
    $this->set_global_mock('Framework',$framework);

    $log_writer = $this->getMock('ILogWriter');
    $log_writer
      ->expects($this->once())
      ->method('describe')
      ->with($this->anything(),$this->equalTo('akuku'))
      ->will($this->returnValue('Akuku'));
    $writer_factory = $this->getMock('ILogWriterFactory');
    $writer_factory
      ->expects($this->once())
      ->method('get_by_verbosity')
      ->with($this->equalTo(42))
      ->will($this->returnValue($log_writer));
    $logger = $this->getMock('Logger',array('classify','add_text','get_log_writer_factory'));
    $logger 
      ->expects($this->once())
      ->method('classify')
      ->will($this->returnValue(array(
        'verbosity'=>42,
        'priority'=>LOG_WARNING
      )));
    $logger
      ->expects($this->once())
      ->method('get_log_writer_factory')
      ->will($this->returnValue($writer_factory));
    $logger
      ->expects($this->once())
      ->method('add_text')
      ->with(LOG_WARNING,LOG_LOCAL0,$host,'Akuku');

    $logger->log('akuku');
 
  }
  private function setServerInfo(){
    $server_info = $this->getMock('IServerInfo');
    $server_info
      ->expects($this->atLeastOnce())
      ->method('get_user')
      ->will($this->returnValue('test'));
    $server_info
      ->expects($this->atLeastOnce())
      ->method('get_host')
      ->will($this->returnValue('testing'));
    $server_info_factory = $this->getMock('IServerInfoFactory');
    $server_info_factory
      ->expects($this->atLeastOnce())
      ->method('from_globals')
      ->will($this->returnValue($server_info));
    $framework = $this->getMock('Framework',array('get_server_info_factory'));
    $framework
      ->expects($this->once())
      ->method('get_server_info_factory')
      ->will($this->returnValue($server_info_factory));
    $this->set_global_mock('Framework',$framework);
  }
  private function getFileSize($file_name){
    $fd = fopen($file_name, 'rb');
    $this->assertTrue(false!==$fd);
    fseek($fd, 0, SEEK_END);
    $size = ftell($fd);
    fclose($fd); 
    return $size;
  }
  public function testLoggerAddsToFile(){
    $this->setServerInfo();
    $log_file_name = '/var/log/nk_dev/test@testing.log';
    if(file_exists($log_file_name)){
      $old_size = $this->getFileSize($log_file_name);
    }else{
      $old_size = 0;
    }

    $logger = new Logger();
    $logger->log('akuku');
    
    for($attempts = 0; $attempts<100; ++$attempts){
      clearstatcache();
      if(file_exists($log_file_name) && $old_size<$this->getFileSize($log_file_name)){
        break;
      }else{
        //waiting for syslog
        usleep(100000);
      }
    }

    $this->assertSame(true,file_exists($log_file_name));
    $new_size = $this->getFileSize($log_file_name);
    $this->assertGreaterThan($old_size,$new_size);
  }
}
?>
