<?php
class DevValidationExceptionExplainer extends MultiInstance implements IValidationExceptionExplainer
{
  private function explain_recursively($path,$tree){
    foreach(Arrays::get($tree,'errors',array()) as $error){
      echo $path . ':' .  get_class($error) . "\t" . $error->getMessage() . "\n";
    }
    foreach(Arrays::get($tree,'fields',array()) as $field_name => $info){
      $this->explain_recursively($path . '/' . $field_name , $info);
    }
  }
  public function explain(IValidationException $e){
    $tree = $e->to_tree();
    ob_start();
    $this->explain_recursively('',$tree);
    return ob_get_clean();
  }
}
?>
