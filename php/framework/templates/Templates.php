<?php
class Templates extends MultiInstance implements ITemplates
{
  private function get_file_name_by_id($id){
    return $id . '.phtml'; 
  }
  public function get_by_id($id){
    $file_name = $this->get_file_name_by_id($id);
    return new Template($file_name);
  }
}
?>
