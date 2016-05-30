<?php
interface IMailComposer
{
  function get_mail();
  function set_from($from_address,$displayed_name=null);
  function add_recipient($email_address,$displayed_name=null);
  function set_subject($subject);
  function set_html($html);
  function set_text($text);
  function add_attachment($displayed_name,$bytes,$mime_type);
  function add_inline_image($cid,$displayed_name,$bytes,$mime_type);
}
