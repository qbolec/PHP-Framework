<?php
interface ITickets
{
  public function generate($id, $ttl);
  /**
   * @throws InvalidTicketException
   */
  public function get_id($ticket);
}
?>
