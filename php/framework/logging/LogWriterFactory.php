<?php
class LogWriterFactory extends Singleton implements ILogWriterFactory
{
  public function get_max_verbosity(){
    return 3;
  }
  public function get_path_log_writer(){
    return PathLogWriter::get_instance();
  }
  public function get_by_verbosity($verbosity){
    switch($verbosity){
    case 0:
      return InfoLogWriter::get_instance();
    case 1:
      return LineLogWriter::get_instance();
    case 2:
      return $this->get_path_log_writer();
    default:
      return StackTraceLogWriter::get_instance();
    }
  }
}
?>
