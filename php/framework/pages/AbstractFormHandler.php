<?php
abstract class AbstractFormHandler extends MethodicHandler implements IPostHandler
{
  protected function get_post_values(IRequestEnv $env, array $name_to_validator){
    $values = array();
    $request = $env->get_request();
    foreach($name_to_validator as $name => $validator){
      $value = $request->get_post_value($name);
      $validator->must_match($value);
      $values[$name] = $value;
    }
    return $values;
  }
}
?>
