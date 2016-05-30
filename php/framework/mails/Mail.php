<?php
class Mail implements IMail{
  private $to;
  private $subject;
  private $message;
  private $headers;
  public function __construct(array $to,$subject,$message,array $headers){
    $this->to = $to;
    $this->subject = $subject;
    $this->message = $message;
    $this->headers = $headers;
  }
  public function send(){
    $to = implode(',',$this->to);
    //https://bugs.php.net/bug.php?id=15841 czyli jednak \n
    $headers = empty($this->headers) ? null : (implode("\n",$this->headers)."\n");
    mail($to,$this->subject,$this->message,$headers);
  }
}
?>
