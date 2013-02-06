<?php
interface IApplication{
  public function run();
  public function get_response(IRequest $request);
}
?>
