<?php
class Tickets extends MultiInstance implements ITickets
{
  private function sign($msg){
    return Framework::get_instance()->get_signatures()->sign($msg);
  }
  private function get_time(){
    return Framework::get_instance()->get_time();
  }
  public function generate($id,$ttl){
    $deadline = $this->get_time()+$ttl;
    $msg = "$deadline/$id";
    $sig = $this->sign($msg);
    return "$sig/$msg";
  }
  public function get_id($ticket){
    $parts = explode('/',$ticket,3);
    if(3==count($parts)){
      list($sig,$deadline,$id) = $parts;
      if($this->sign("$deadline/$id")==$sig){
        if($this->get_time()<$deadline){
          return $id;
        } 
      }
    }
    throw new InvalidTicketException($ticket);
  }
}
?>
