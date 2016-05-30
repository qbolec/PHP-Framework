<?php
class MailComposer implements IMailComposer{
  private $subject;
  private $html;
  private $text;
  private $attachments = array();
  private $inline_images = array();
  private $to=array();
  private $from_address;
  private $unsubscribe_link;
  private function is_multipart($tree){
    return array_key_exists('parts',$tree);
  }
  private function purge($tree){
    if($this->is_multipart($tree)){
      $parts = array();
      foreach($tree['parts'] as $name => $part){
        $parts[$name] = $this->purge($part);
      }
      if(count($parts)==1){
        return $part;
      }
      $tree['parts'] = $parts;
      return $tree;
    }else{
      return $tree;
    }
  }
  private function guid(){
    $rng = Framework::get_instance()->get_rng();
    $time = Framework::get_instance()->get_time();
    return sha1($rng->next() . '|' . $time . '|shoo');
  }
  private function base64_encode($bytes){
    return preg_split('/\r?\n\r?/',chunk_split(base64_encode($bytes)),-1,PREG_SPLIT_NO_EMPTY);
  }
  private function to_lines($tree){
    $lines = array();
    $boundary = $this->guid();
    foreach($tree['headers'] as $key => $value){
      if($key==='Content-Type' && $this->is_multipart($tree)){
        $value .= '; boundary="'. $boundary .'"';
      }
      $lines[] = "$key: $value";//encoding
    }
    $lines[] = '';
    if($this->is_multipart($tree)){
      foreach($tree['parts'] as $part){
        $lines[] = '--' . $boundary;
        $lines = Arrays::concat($lines,$this->to_lines($part));
      }
      $lines[] = '--' . $boundary . '--';
    }else{
      $lines = Arrays::concat($lines,$tree['content']);
    }
    return $lines;
  }
  public function get_mail(){
    $shape = array(
      'headers' => array(
        'Content-Type' => "multipart/related",
      ),
      'parts' => array(
        'body' => array(
          'headers' => array(
            'Content-Type' => "multipart/alternative",
          ),
          'parts' => array(
          )
        )
      )
    );
    if($this->text !== null){
      $shape['parts']['body']['parts']['text'] = array(
        'headers' => array(
          'Content-Type' => 'text/plain; charset="utf-8"',
          'Content-Transfer-Encoding' =>  'base64',
        ),
        'content' => $this->base64_encode($this->text),
      );
    }
    if($this->html !== null){
      $shape['parts']['body']['parts']['html'] = array(
        'headers' => array(
          'Content-Type' => 'text/html; charset="utf-8"',
          'Content-Transfer-Encoding' =>  'base64',
        ),
        'content' => $this->base64_encode($this->html),
      );
    }
    foreach($this->inline_images as $i => $inline_image){
      $shape['parts']['image/'.$i] = array(
        'headers' => array(
          'Content-Type' =>  $inline_image['mime_type'] .  '; name="' . $inline_image['displayed_name'] . '"',//escape?
          'Content-Description' => $inline_image['displayed_name'],
          'Content-Disposition' => 'inline; filename="' . $inline_image['displayed_name'] . '"; size=' . strlen($inline_image['bytes']), //creation-date, modification-date ?
          'Content-ID' => '<' . $inline_image['cid'] . '>',//escape?
          'Content-Transfer-Encoding' => 'base64',
        ),
        'content' => $this->base64_encode($inline_image['bytes'])
      );
    }
    foreach($this->attachments as $i => $attachment){
      $shape['parts']['attachment/'.$i] = array(
        'headers' => array(
          'Content-Type' =>  $attachment['mime_type'] .  '; name="' . $attachment['displayed_name'] . '"',//escape?
          'Content-Description' => $attachment['displayed_name'],
          'Content-Disposition' => 'attachment; filename="' . $attachment['displayed_name'] . '"; size=' . strlen($attachment['bytes']), //creation-date, modification-date ?
          'Content-Transfer-Encoding' => 'base64',
        ),
        'content' => $this->base64_encode($attachment['bytes'])
      );
    }
    $tree = $this->purge($shape);
    $lines = $this->to_lines($tree);
    //everything till the first empty line is a header
    $pos=array_search('',$lines,true);
    Framework::get_instance()->get_assertions()->halt_if($pos===FALSE);
    $headers = array(
      "MIME-Version: 1.0",
    );
    if($this->from_address){
      $headers[] = 'From: ' . $this->from_address; //encoding?
      $headers[] = 'Return-Path: ' . $this->from_address; //encoding?
    }
    if($this->unsubscribe_link){
      $headers[] = 'List-Unsubscribe: <' . $this->unsubscribe_link . '>';
    }
    $headers = Arrays::concat($headers,array_slice($lines,0,$pos));
    $body = implode("\n",array_slice($lines,$pos+1));
    return Framework::get_instance()->get_mails()->get_mail($this->to,$this->subject,$body,$headers);
  }
  private function encode_rfc_1342($text){
    return '=?UTF-8?B?' . base64_encode($text) . '?=';
  }
  private function encode_email_address($address,$name){
    if($name==null){
      return $address;
    }else{
      return $this->encode_rfc_1342($name) . ' <' . $address . '>';
    }
  }
  public function set_unsubscribe_link($url){
    $this->unsubscribe_link = $url;
  }
  public function set_from($from_address,$displayed_name=null){
    $this->from_address = $this->encode_email_address($from_address,$displayed_name);
  }
  public function add_recipient($email_address,$displayed_name=null){
    $this->to[]=  $this->encode_email_address($email_address,$displayed_name);
  }
  public function set_subject($subject){
    $this->subject =  $this->encode_rfc_1342($subject);
  }
  public function set_html($html){
    $this->html = $html;
  }
  public function set_text($text){
    $this->text = $text;
  }
  public function add_attachment($displayed_name,$bytes,$mime_type){
    $this->attachments[] = array(
      'mime_type'=>$mime_type,
      'displayed_name'=>$displayed_name,
      'bytes'=>$bytes,
    );
  }
  public function add_inline_image($cid,$displayed_name,$bytes,$mime_type){
    $this->inline_images[] = array(
      'cid'=>$cid,
      'mime_type'=>$mime_type,
      'displayed_name'=>$displayed_name,
      'bytes'=>$bytes,
    );
  }
}
