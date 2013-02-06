<?php
class JSONValidationExceptionExplainer extends MultiInstance implements IValidationExceptionExplainer
{
  private function to_array($tree){
    $encoded = array();
    foreach(Arrays::get($tree,'errors',array()) as $error){
      $encoded['errors'][] =  array(
        'class' => get_class($error) , 
        'message' => $error->getMessage() 
      );
    }
    foreach(Arrays::get($tree,'fields',array()) as $field_name => $info){
      $encoded['fields'][$field_name] = $this->to_array($info);
    }
    return $encoded;
  }
 
  public function explain(IValidationException $e){
    $tree = $e->to_tree();
    $arr=$this->to_array($tree);
    return JSON::encode($arr);
  }
}
?>
