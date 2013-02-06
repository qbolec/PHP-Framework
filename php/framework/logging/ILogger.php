<?php
interface ILogger{
  public function log($info=null);
  public function get_log_writer_factory();
}
?>
