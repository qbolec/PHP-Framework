<?php
interface ILogClassifier{
  public function classify(array $backtrace,$info);
}
?>
