<?php
abstract class AbstractConfig extends MockableSingleton implements IConfig
{
  private $tree = null;
  abstract protected function get_tree();
  private $assertions;
  public function get($path){
    if(null===$this->tree){
      $this->tree = $this->get_tree();
    }
    if(''===$path){
      return $this->tree;
    }
    if(null === $this->assertions){
      $this->assertions = Framework::get_instance()->get_assertions();
    }
    $this->assertions->halt_unless(
      preg_match('@^[^/]+(/[^/]+)*$@',$path)
    );
    $edges = explode('/',$path);
    $subtree = $this->tree;
    foreach($edges as $i=>$edge){
      if(is_array($subtree) && array_key_exists($edge,$subtree)){
        $subtree = $subtree[$edge];
      }else{
        throw new MissingKeyConfigException(implode('/',array_slice($edges,0,$i+1)));
      }  
    }
    return $subtree;
  }
}
?>
