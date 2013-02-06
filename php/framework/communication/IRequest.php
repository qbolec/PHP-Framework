<?php
interface IRequest{
  const METHOD_OPTIONS = 'OPTIONS';
  const METHOD_GET = 'GET';
  const METHOD_HEAD = 'HEAD';
  const METHOD_POST = 'POST';
  const METHOD_PUT = 'PUT';
  const METHOD_DELETE = 'DELETE';
  const METHOD_TRACE = 'TRACE';
  const METHOD_CONNECT = 'CONNECT';
  public function get_uri_param($name,$default_value=null);
  public function get_post_value($name,$default_value=null);
  public function get_host();
  public function get_port();
  public function get_path();
  public function get_uri();//for OAuth
  public function get_query();
  public function get_scheme();
  public function get_method();
  public function is_post();
  public function is_https();
  public function get_header($name,$default_value=null);
  public function get_body();
}
?>
