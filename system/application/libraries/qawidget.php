<?php


class qaWidget
{
  private $CI;

  function __construct()
  {
    $this->CI =& get_instance();
  }

  public function getModel($type)
  {
    $mapping = array(
      'category',
      'brand',
      'product'
    );

    return in_array($type, $mapping) ? $type : null;
  }

  public function send_vote_email_to_team($team_id, $user_id)
  {
    $members = $this->CI->team_member->getVoteEmailMembers($team_id);

    $subject = "user has voted for widget";
    $message = "user has voted for widget";

    if($members)
    {
      foreach ($members as $member)
      {
        if($user_id == $member['qa_user_id'])
          continue;

        mail($member['email'], $subject, $message, "Content-Type: text/html");
      }
    }
  }

  public function send_comment_email_to_team($team_id, $post)
  {
    $members = $this->CI->team_member->getCommentEmailMembers($team_id);

    $subject = "User has posted a comment on ".$post['item_title'];
    $message = "User has posted a comment <br/><br/><strong>\"".$post['qa_title']."\"</strong><br/>".$post['qa_description'];

    if($members)
    {
      foreach ($members as $member)
      {
        if($post['qa_user_id'] == $member['qa_user_id'])
          continue;

        mail($member['email'], $subject, $message, "Content-Type: text/html");
      }
    }
  }

  public function send_email_question_creator($widget_data, $answer, $question)
  {
    if($question->qa_user_id == $answer['qa_user_id'])
      return false;

    $user = $this->CI->qa_user->getUserById($question->qa_user_id);

    if($user && trim($user[0]['email']))
    {
      $subject = "User has posted a comment on ".$widget_data['store_name'];
      $message = "User has posted a comment <br/><br/><strong>\"".$answer['qa_title']."\"</strong><br/>".$answer['qa_description'];
      
      mail($user[0]['email'], $subject, $message, "Content-Type: text/html");
    }
  }
}