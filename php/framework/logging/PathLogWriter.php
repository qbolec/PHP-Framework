<?php
class PathLogWriter extends InfoLogWriter
{
  public function describe(array $backtrace,$info){
    return $this->get_path($backtrace) . '>' .  parent::describe($backtrace,$info);
  }
  private function get_path(array $backtrace){
    // debug_backtrace zwraca tablicę w której każdy element miesza w sobie dwie rzeczy:
    // miejsce wywołania (file, line) oraz co zostało wywołane (class, function)
    // w efekcie jeśli interesuje nas linijka 5 z Foo::bar, to musimy polować na
    // Foo i bar w elemencie backtrace[$i+1], zaś na
    // 5 w elemencie backtrace[$i] 

    $length = count($backtrace);
    $my_backtrace = array();
    //najgłębsze nie ma linii
    $my_backtrace[0]['line'] = -2; 
    foreach($backtrace as $i => $frame){
      $class = Arrays::get($frame,'class','unknown-class');
      $function = Arrays::get($frame,'function','unknown-function');
      $line = Arrays::get($frame,'line',-1);
      $my_backtrace[$i]['where'] = "$class::$function";
      $my_backtrace[$i+1]['line'] = $line;
    }
    $my_backtrace[$length]['where'] = Arrays::get($backtrace[$length-1],'file','unknown-file');
    $path = array();
    foreach($my_backtrace as $i => $frame){
      $path[] = $frame['where'] . '@' . $frame['line'];
    }

    return implode('/',array_reverse($path,false));
  }
}
?>
