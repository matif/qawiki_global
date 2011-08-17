<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of team_invites
 *
 * @author purelogics
 */
class team_invites extends Model
{
  //put your code here
  function team_invites()
  {
    parent::Model();
  }
  function addInvite($data)
  {    
    $this->db->insert('team_invites',$data);
  }
  function getInvites($uid)
  {
    $sqlQuery ="SELECT invite.invite_id, invite.qa_team_id, invite.unique_code, invite.email, store.qa_user_id, store.qa_store_name
          FROM team_invites AS invite
          INNER JOIN qa_team AS team ON invite.qa_team_id =  team.qa_team_id
          INNER JOIN qa_store AS store ON team.qa_store_id = store.qa_store_id          
          WHERE invite.qa_user_id = $uid
      ";
    return $this->db->query($sqlQuery)->result_array();
  }
  function deleteInvites($invite_id)
  {
    $this->db->where('invite_id', $invite_id);
    $this->db->delete('team_invites');
  }

  function already_invited($user_id, $team_id)
  {
    $this->db->where('qa_user_id', $user_id);
    $this->db->where('qa_team_id', $team_id);
    $this->db->limit(1);
    $result = $this->db->get('team_invites')->result_array();    
    return ($result) ? $result[0] : null;
  }
  
  function already_invited_email($email, $team_id)
  {
    $this->db->where('email', $email);
    $this->db->where('qa_team_id', $team_id);
    $this->db->limit(1);
    $result = $this->db->get('team_invites')->result_array();
    return ($result) ? $result[0] : null;
  }
  function getInviteByCode($code)
  {
    $this->db->where('unique_code',$code);
    $result = $this->db->get('team_invites')->result_array();
    return ($result) ? $result[0] : null;
  }
  
  function deleteInvitationByCode($code)
  {
    $this->db->where('unique_code', $code);
    $this->db->delete('team_invites');
  }

  function get_inactive_members($store_id, $team_id)
  {
    $query = '
      SELECT user.name,user.qa_user_id,invite.email, "invited" as status
      FROM team_invites AS invite
      INNER JOIN qa_user as user ON user.qa_user_id = invite.qa_user_id
      WHERE invite.qa_team_id = '. $team_id;
    
    return $this->db->query($query)->result_array();
  }
}