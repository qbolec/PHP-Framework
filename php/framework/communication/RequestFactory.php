<?php
class RequestFactory extends MultiInstance implements IRequestFactory
{
  //@author from OAuth library
  // helper to try to sort out headers for people who aren't running apache
  private static function get_headers() {
    if (function_exists('apache_request_headers')) {
      // we need this to get the actual Authorization: header
      // because apache tends to tell us it doesn't exist
      $headers = apache_request_headers();

      // sanitize the output of apache_request_headers because
      // we always want the keys to be Cased-Like-This and arh()
      // returns the headers in the same case as they are in the
      // request
      $out = array();
      foreach ($headers AS $key => $value) {
        $key = str_replace(
            " ",
            "-",
            ucwords(strtolower(str_replace("-", " ", $key)))
          );
        $out[$key] = $value;
      }
    } else {
      // otherwise we don't have apache and are just going to have to hope
      // that $_SERVER actually contains what we need
      $out = array();
      if( isset($_SERVER['CONTENT_TYPE']) )
        $out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
      if( isset($_ENV['CONTENT_TYPE']) )
        $out['Content-Type'] = $_ENV['CONTENT_TYPE'];

      foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) == "HTTP_") {
          // this is chaos, basically it is just there to capitalize the first
          // letter of every word that is not an initial HTTP and strip HTTP
          // code from przemek
          $key = str_replace(
            " ",
            "-",
            ucwords(strtolower(str_replace("_", " ", substr($key, 5))))
          );
          $out[$key] = $value;
        }
      }
    }
    return $out;
  }
  private function get_body(){
    $body = StdLib::get_instance()->file_get_contents('php://input');
    return $body;
  }
  public function from_globals(){
    return new Request($_POST,$_GET,$_SERVER,$this->get_headers(),$this->get_body());
  }
  public function from_method_url_post_data($method,$url,array $post_data){
    $url_info=parse_url($url);
    Framework::get_instance()->get_assertions()->halt_if(false===$url_info);
    if(preg_match("@^([^/]+)(//)([^/]+)(/|$)(.*)$@",$url,$m)){
      $request_uri =  '/' . $m[5];
    }else{
      Framework::get_instance()->get_logger()->log();
      $request_uri = $url;
    }

    $server = array(
      'SERVER_NAME' => Arrays::get($url_info,'host',Arrays::get($_SERVER,'SERVER_NAME')),
      'SERVER_PORT' => Arrays::get($url_info,'port',Arrays::get($_SERVER,'SERVER_PORT',80)),
      'SCRIPT_URL' => Arrays::get($url_info,'path','/'),
      'REQUEST_URI' => $request_uri,
      'QUERY_STRING' => Arrays::get($url_info,'query',''),
    );
    if(Arrays::get($url_info,'scheme')=='https'){
      $server['HTTPS'] = 'on';
    }
    $server['REQUEST_METHOD']=$method;
    parse_str($server['QUERY_STRING'],$get_data);
    return new Request($post_data,$get_data,$server,array(),'');
  }
}
?>
