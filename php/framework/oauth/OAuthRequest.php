<?php
class OAuthRequest {
  protected $parameters;
  protected $http_method;
  protected $http_url;
  // for debug purposes
  public $base_string;
  public static $version = '1.0';

  public function __construct($http_method, $http_url, $parameters) {
    $this->parameters = $parameters;
    $this->http_method = $http_method;
    $this->http_url = $http_url;
  }


  /**
   * attempt to build up a request from what was passed to the server
   */
  public static function from_request(IRequest $request){
    $scheme = $request->get_scheme();
    $http_url =  
      $scheme .
      '://' . $request->get_host().
      ':' .
      $request->get_port().
      $request->get_uri();
    $http_method = $request->get_method();

    // We weren't handed any parameters, so let's find the ones relevant to
    // this request.
    // If you run XML-RPC or similar you should use this to provide your own
    // parsed parameter-list
    // Parse the query-string to find GET parameters
    $parameters = OAuthUtil::parse_parameters($request->get_query());

    // It's a POST request of the proper content-type, so parse POST
    // parameters and add those overriding any duplicates from GET
    if ($request->is_post()
      && strstr($request->get_header('Content-Type',''),
        'application/x-www-form-urlencoded')
      ) {
        $post_data = OAuthUtil::parse_parameters(
          $request->get_body()
        );
        $parameters = array_merge($parameters, $post_data);
      }

    // We have a Authorization-header with OAuth data. Parse the header
    // and add those overriding any duplicates from GET or POST
    $authorization_header = $request->get_header('Authorization','');
    if (substr($authorization_header, 0, 6) == 'OAuth ') {
      $header_parameters = OAuthUtil::split_header(
        $authorization_header
      );
      $parameters = array_merge($parameters, $header_parameters);
    }


    return new OAuthRequest($http_method, $http_url, $parameters);
  }
  
  /** 
   * pretty much a helper function to set up the request 
   */ 
  public static function extend_parameters(array $parameters, $body, OAuthConsumer $consumer) { 
    $defaults = array(
      "oauth_version" => OAuthRequest::$version, 
      "oauth_nonce" => OAuthRequest::generate_nonce(), 
      "oauth_timestamp" => OAuthRequest::generate_timestamp(), 
      "oauth_consumer_key" => $consumer->key,
    );
    if(null!==$body){
      $defaults['oauth_body_hash'] = base64_encode(sha1($body,true));
    }
    return array_merge($defaults, $parameters); 
  }

  private function set_parameter($key, $value){
    $this->parameters[$key] = $value;
  }
  
  public function get_parameter($name) {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
  }

  public function get_parameters() {
    return $this->parameters;
  }


  /**
   * The request parameters, sorted and concatenated into a normalized string.
   * @return string
   */
  public function get_signable_parameters() {
    // Grab all parameters
    $params = $this->parameters;

    // Remove oauth_signature if present
    // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
    if (isset($params['oauth_signature'])) {
      unset($params['oauth_signature']);
    }

    return OAuthUtil::build_http_query($params);
  }

  /**
   * Returns the base string of this request
   *
   * The base string defined as the method, the url
   * and the parameters (normalized), each urlencoded
   * and the concated with &.
   */
  public function get_signature_base_string() {
    $parts = array(
      $this->get_normalized_http_method(),
      $this->get_normalized_http_url(),
      $this->get_signable_parameters()
    );

    $parts = OAuthUtil::urlencode_rfc3986($parts);

    return implode('&', $parts);
  }

  /**
   * just uppercases the http method
   */
  public function get_normalized_http_method() {
    return strtoupper($this->http_method);
  }

  /**
   * parses the url and rebuilds it to be
   * scheme://host/path
   */
  public function get_normalized_http_url() {
    $parts = parse_url($this->http_url);

    $scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
    $port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
    $host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
    $path = (isset($parts['path'])) ? $parts['path'] : '';

    if (($scheme == 'https' && $port != '443')
        || ($scheme == 'http' && $port != '80')) {
      $host = "$host:$port";
    }
    return "$scheme://$host$path";
  }

  /**
   * builds a url usable for a GET request
   */
  public function to_url() {
    $post_data = $this->to_postdata();
    $out = $this->get_normalized_http_url();
    if ($post_data) {
      $out .= '?'.$post_data;
    }
    return $out;
  }

  /**
   * builds the data one would send in a POST request
   */
  public function to_postdata() {
    return OAuthUtil::build_http_query($this->parameters);
  }

  /**
   * builds the Authorization: header
   */
  public function to_header($realm=null) {
    $first = true;
    if($realm) {
      $out = 'Authorization: OAuth realm="' . OAuthUtil::urlencode_rfc3986($realm) . '"';
      $first = false;
    } else {
      $out = 'Authorization: OAuth';
    }
    $total = array();
    foreach ($this->parameters as $k => $v) {
      if (substr($k, 0, 5) != "oauth") continue;
      if (is_array($v)) {
        throw new OAuthException('Arrays not supported in headers');
      }
      $out .= ($first) ? ' ' : ',';
      $out .= OAuthUtil::urlencode_rfc3986($k) .
              '="' .
              OAuthUtil::urlencode_rfc3986($v) .
              '"';
      $first = false;
    }
    return $out;
  }

  public function __toString() {
    return $this->to_url();
  }
  
  public function sign_request($signature_method, $consumer, $token) { 
    $this->set_parameter( 
      "oauth_signature_method", 
      $signature_method->get_name() 
    ); 
    $signature = $this->build_signature($signature_method, $consumer, $token); 
    $this->set_parameter("oauth_signature", $signature); 
  } 

  public function build_signature($signature_method, $consumer, $token) {
    $signature = $signature_method->build_signature($this, $consumer, $token);
    return $signature;
  }

  /**
   * util function: current timestamp
   */
  private static function generate_timestamp() {
    return Framework::get_instance()->get_time();
  }

  /**
   * util function: current nonce
   */
  private static function generate_nonce() {
    $mt = microtime();
    $rand = mt_rand();

    return md5($mt . $rand); // md5s look nicer than numbers
  }
}
?>
