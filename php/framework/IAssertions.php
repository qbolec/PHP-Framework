<?php
interface IAssertions
{
  public function halt_if($condition);
  public function warn_if($condition);
  public function halt_unless($condition);
  public function warn_unless($condition);
}
?>
