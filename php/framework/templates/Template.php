<?php
class Template implements ITemplate
{
  private $file_name;
  public function __construct($file_name){
    $this->file_name = $file_name;
  }
  public function get_text(array $params = array()){
    ob_start();
    include $this->file_name;
    return ob_get_clean();
  }
  private function get_templates(){
    return Framework::get_instance()->get_templates();
  }
  private function get_template_by_id($id){
    return $this->get_templates()->get_by_id($id);
  }
  private function get_template_text($id, array $params){
    return $this->get_template_by_id($id)->get_text($params);
  }
  private function out($value){
    echo Strings::to_html($value);
  }
}
?>
