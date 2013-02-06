<?php
abstract class AbstractEditableEntity extends AbstractEntity implements IEditableEntity
{
  private $original_data = array();
  private $open_transactions_count = 0;
  protected function set_field($field_name,$new_value){
    $lock = $this->begin();
    if(!array_key_exists($field_name,$this->original_data)){
      $this->original_data[$field_name] = Arrays::grab($this->data,$field_name);
    }
    $this->data[$field_name] = $new_value;
    $this->commit($lock);
  }
  private function get_lock_prefix(){
    return get_class($this);
  }
  private function get_lock(){
    return Framework::get_instance()->get_lock_factory()->get_lock($this->get_lock_prefix().'/'.$this->get_id());
  }
  public function begin(){
    if($this->open_transactions_count++){
      return null;
    }else{
      $lock = $this->get_lock();
      //it may happen, that before we took the lock, someone edited the entity remotely
      $this->data = $this->get_editable_family()->get_fresh_data($this->get_id());
      return $lock;
    }
  }
  public function commit(ILock $lock = null){
    --$this->open_transactions_count;
    $this->get_assertions()->halt_if($this->open_transactions_count<0);
    if(0==$this->open_transactions_count){
      $this->save();
      $lock->release();
    }  
  }
  private function get_assertions(){
    return Framework::get_instance()->get_assertions();
  }
  private function get_editable_family(){
    $family = $this->get_family();
    $this->get_assertions()->halt_unless($family instanceof IEditableEntities);
    return $family; 
  }
  private function save(){
    $this->get_editable_family()->save($this->data,$this->original_data);
    $this->original_data = array();
  }
}
?>
