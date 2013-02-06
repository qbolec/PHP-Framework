<?php
class StdLib extends MockableSingleton implements IStdLib{
  function file_get_contents($filename){
    return file_get_contents($filename);
  }
}
?>
