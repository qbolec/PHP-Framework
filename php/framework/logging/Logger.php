<?php
class Logger extends Singleton implements ILogger
{
  public function log_exception(Exception $e){
    $this->log($e->__toString());
  }
  public function log($info=null){
    $backtrace = debug_backtrace(0);
    $classification = $this->classify($backtrace,$info);
    $priority = $classification['priority'];
    if($priority!==null){
      $text = $this->describe($classification['verbosity'],$backtrace,$info);
      $ident = $this->get_ident();
      $facility = LOG_LOCAL0;
      $this->add_text($priority,$facility,$ident,$text);
    }
  }
  private function get_request(){
    return Framework::get_instance()->get_request_factory()->from_globals();
  }
  private function get_server_info(){
    return Framework::get_instance()->get_server_info_factory()->from_globals();
  }
  protected function get_ident(){
    $request = $this->get_request();
    $host = $request->get_host();
    if(null===$host){
      $server_info = $this->get_server_info();
      return $server_info->get_user() . '@' . $server_info->get_host();
    }else{
      return $host;
    }
  }
  protected function describe($verbosity,$backtrace,$info){
    return $this->get_log_writer_factory()->get_by_verbosity($verbosity)->describe($backtrace,$info);
  }
  public function get_log_writer_factory(){
    return LogWriterFactory::get_instance();
  }
  protected function get_classifier(){
    return LogClassifier::get_instance();
  }
  protected function classify(array $backtrace,$info){
    return $this->get_classifier()->classify($backtrace,$info);
  }
  protected function add_text($priority,$facility,$ident,$text){
    openlog($ident,LOG_PID,$facility);
    syslog($priority,$text);
    closelog();
  }
}
?>
