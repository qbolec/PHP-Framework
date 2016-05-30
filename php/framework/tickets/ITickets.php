<?php
interface ITickets
{
  public function generate($id, $ttl, $secret='');
  /**
   * @throws InvalidTicketException
   */
  public function get_id($ticket, $secret='');
  /**
   * @throws IsMissingException
   */
  public function peek_id($ticket);
}
?>
