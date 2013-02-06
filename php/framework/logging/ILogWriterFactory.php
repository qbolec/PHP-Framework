<?php
interface ILogWriterFactory{
  public function get_by_verbosity($verbosity);
  public function get_max_verbosity();
  public function get_path_log_writer();
}
?>
