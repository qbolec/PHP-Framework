<?php
class UnexpectedMembersException extends MultiValidationException
{
  public function __construct(array $members){
    $errors = array();
    foreach($members as $member){
      $errors[]= new UnexpectedMemberException($member);
    }
    parent::__construct($errors);
  }
}
?>
