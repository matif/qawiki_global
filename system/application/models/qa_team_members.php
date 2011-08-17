<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stores
 *
 * @author purelogics
 */
class qa_team_members extends MY_Model {
    //put your code here
  function qa_team_members()   
  {
	parent::Model();
	$this->table = 'qa_team_member';
	$this->primary_key = 'qa_team_member_id';		
  }
  
  
  function addTeamMember($data)
  {
    $this->db->insert('qa_team_member', $data);
    
    $CI =& get_instance();
    $CI->load->model('qa_teams', 'team_model');
    $CI->load->helper('widget');
    
    $team_info = $CI->team_model->getTeamById($data['qa_team_id']);
    
    if($team_info)
    {
      $CI->store_id = $team_info['qa_store_id'];
      
      if(!isset($CI->custom_config) || !isset($CI->custom_config['default_css']))
      {
        require_once APPPATH . 'config/custom/settings.php';
      }
      
      require_once APPPATH . 'libraries/qaCssParser.php';
      
      $cssParser = new qaCssParser($CI->store_id);
      $cssParser->saveDefaultCSS($CI->custom_config['default_css']);
    }
  }

  function updateTeamMember($data , $team_member_id,$team_id){
    //embed_code    
     $this->db->where('qa_team_member_id',$team_member_id);
     $this->db->where('qa_team_id',$team_id);
     $this->db->update('qa_team_member',$data);     
  }
  function deleteTeamMember($team_member_id, $team_id, $user_id)
  {
    $sqlQuery = 'Delete FROM qa_team_member WHERE qa_team_member_id='.$team_member_id;
    $this->db->query($sqlQuery);
    
    $CI =& get_instance();
    $CI->load->model('qa_teams', 'team_model');
    
    $team_info = $CI->team_model->getTeamById($team_id);
    
    if($team_info)
    {
      delete_user_store_directory($team_info['qa_store_id'], $user_id);
    }
  }

  function addOwnerAsMember($team_id, $user_id, $role_id = 0)
  {
    $data = array(
      'qa_team_id'           => $team_id,
      'qa_user_id'           => $user_id,
      'role'                 => 'creator',
      'notify_me_on_comment' => 0,
      'notify_me_on_vote'    => 0,
      'qa_created'           => date("Y-m-d"),
      'moderation_group_id'  => $role_id
    );
    
    $this->addTeamMember($data);
  }
  
  function getTeamMemberById($team_member_id)
  {
	$sqlQuery = "SELECT *
		FROM `qa_team_member` 
		WHERE qa_team_member_id = ".mysql_escape_string($team_member_id)."
		";	
    return $this->db->query($sqlQuery)->result();
  }
      
  function getTeamMembers($qa_team_id, $offset, $limit)
  {
	$sqlQuery = "SELECT qu.name,qam.qa_created, qam.role, qam.notify_me_on_comment, qam.notify_me_on_vote, qt.team_name, qam.qa_team_member_id, qam.qa_team_id,qam.image_url,qam.designation, qu.email
		FROM `qa_team_member` AS qam
		INNER JOIN qa_team AS qt ON qt.`qa_team_id` = qam.qa_team_id		
		INNER JOIN qa_user AS qu ON qu.`qa_user_id` = qam.qa_user_id
		WHERE qam.qa_team_id = ".mysql_escape_string($qa_team_id)."
		LIMIT $offset, $limit
		";
        $result = $this->db->query($sqlQuery)->result_array();
//        echo $this->db->last_query();
    return  $result;
  }  
  function getTeamMembersCount($qa_team_id) {
    
	$sqlQuery = "SELECT COUNT( qam.`qa_team_member_id` ) as CNT
		FROM `qa_team_member` AS qam
		INNER JOIN qa_team AS qt ON qt.`qa_team_id` = qam.qa_team_id		
		INNER JOIN qa_user AS qu ON qu.`qa_user_id` = qam.qa_user_id
		WHERE qam.qa_team_id = ".mysql_escape_string($qa_team_id)."";
	
     $result = $this->db->query($sqlQuery)->result_array();
     $result[0]['CNT'];
     return $result[0]['CNT'];
  }
  function updateStoreStyle($data , $user_id , $team_id)
  {    
    $sqlQuery = "UPDATE qa_team_member SET `widget_settings`='".$data."' WHERE `qa_user_id`='$user_id' AND `qa_team_id` = '$team_id'";
    $this->db->query($sqlQuery);
  }

  function getMemberSettings($user_id, $team_id)
  {
    $this->db->where('qa_user_id', $user_id);
    $this->db->where('qa_team_id', $team_id);
    $this->db->limit(1);

    $result = $this->db->get('qa_team_member')->result_array();
    
    return ($result) ? $result[0] : null;
  }
  function is_team_member($user_id, $team_id)
  {
    $this->db->where('qa_user_id', $user_id);
    $this->db->where('qa_team_id', $team_id);
    $this->db->limit(1);

    $result = $this->db->get('qa_team_member')->result_array();

    return ($result) ? $result[0] : null;
  }

  function get_badges(&$questions, $team_id)
  {
    $ids = array();
    foreach($questions as &$question)
    {
      $ids[] = $question['qa_user_id'];
    }

    if(!empty($ids))
    {
      $this->db->select('qa_user_id, image_url as badge_image, designation');
      $this->db->where('qa_team_id', $team_id);
      $this->db->where_in('qa_user_id', $ids);

      $members = $this->db->get('qa_team_member')->result_array();

      foreach($members as $member)
      {
        foreach($questions as &$question)
        {
          if($question['qa_user_id'] == $member['qa_user_id'])
          {
            $question  = array_merge($question, $member);
            break;
          }
        }
      }
    }
  }
  function checkCurrentUserRole($uid, $team_id)
  {
    $this->db->select('role');
    $this->db->where('qa_user_id', $uid);
    $this->db->where('qa_team_id', $team_id);
    $result = $this->db->get('qa_team_member')->result_array();
    
    return (isset($result[0]) ? $result[0] : '');
  }

  function getVoteEmailMembers($team_id)
  {
    $this->db->select('u.email, u.qa_user_id');
    $this->db->where('tm.qa_team_id', $team_id);
    $this->db->where('tm.notify_me_on_vote', 1);
    $this->db->join('qa_user u', 'tm.qa_user_id = u.qa_user_id');

    return $this->db->get('qa_team_member tm')->result_array();
  }

  function getCommentEmailMembers($team_id)
  {
    $this->db->select('u.email, u.qa_user_id');
    $this->db->where('tm.qa_team_id', $team_id);
    $this->db->where('tm.notify_me_on_comment', 1);
    $this->db->join('qa_user u', 'tm.qa_user_id = u.qa_user_id');

    return $this->db->get('qa_team_member tm')->result_array();
  }
//  function deleteTeamMembers($store_id)
//  {
//    $sql = 'DELETE FROM qa_team_member
//      WHERE qa_team_id = SELECT qa_team_id
//      FROM qa_team
//      WHERE qa_store_id = '.$store_id;
//
//    $this->db->query($sql);
//  }

  function getWebInfo($team_id, $member_id,$store_id)
  {
    $this->db->select("qa_site_name,qa_login_url,qa_thanks_url");
    $this->db->where('qa_team_id',$team_id);
    $this->db->where('qa_team_member_id',$member_id);
    return $this->db->get("qa_team_member")->result();
  }

  function get_web_ring_members($store_id, $type = "",$order_by = "asc" )
  {
    $this->db->select("user.name,user.qa_user_id,team.qa_team_id");
    $this->db->from("qa_team as team");
    $this->db->join("qa_team_member as member", "team.qa_team_id = member.qa_team_id");
    $this->db->join("qa_user as user", "user.qa_user_id = member.qa_user_id");
    $this->db->where("qa_store_id",$store_id);
    if($type == "")
      $this->db->where("member.is_active",NULL);
    else
    {
      $this->db->where("member.is_active",1);      
      
    }
    $this->db->order_by("user.name",$order_by);
    return $this->db->get()->result_array();
  } 
  
  function update_status($team_id,$in_array,$data)
  {
    $this->db->where("qa_team_id",$team_id);
    $this->db->where_in("qa_user_id",$in_array);
    $this->db->update('qa_team_member', $data); 
  }
}
