<?php
interface ILogWriter
{
  public function describe(array $backtrace,$info);
}
?>
