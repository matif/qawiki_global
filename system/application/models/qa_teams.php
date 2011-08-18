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
class qa_teams extends MY_Model {
    //put your code here
  function qa_teams()
  {
    parent::Model();

    $this->table = 'teams';
    $this->primary_key = 'qa_team_id';
  }

  function addTeam($data)
  {    
    $this->db->insert('teams', $data);

    return $this->db->insert_id();
  }
  
  function getTeams($user_id, $offset=0, $limit=0)
  {
  
    if($offset==0 && $limit==0) {
      $sqlQuery = "SELECT qa.team_name, s.qa_store_name, qa.qa_team_id, tm.role,qa.qa_store_id
        FROM `team_members` AS tm
        INNER JOIN teams AS qa ON qa.`qa_team_id` = tm.qa_team_id
        INNER JOIN stores AS s ON qa.`qa_store_id` = s.qa_store_id
        WHERE tm.qa_user_id = ".mysql_escape_string($user_id);
    } else {
      $sqlQuery = "SELECT qa.team_name, s.qa_store_name, qa.qa_team_id, tm.role
        FROM `team_members` AS tm
        INNER JOIN teams AS qa ON tm.`qa_team_id` = qa.qa_team_id
        INNER JOIN stores AS s ON qa.`qa_store_id` = s.qa_store_id        
        WHERE tm.qa_user_id = ".mysql_escape_string($user_id)."
        LIMIT $offset, $limit";
    }
    return $this->db->query($sqlQuery)->result();
  }
  function getTeamById($team_id)
  {
    $sqlQuery = "SELECT *
      FROM `teams`
      WHERE `qa_team_id` = ".mysql_escape_string($team_id)."
    ";

    $result = $this->db->query($sqlQuery)->result_array();
    
    return ($result) ? $result[0] : null;
  }  
  function getTeamCount($user_id) {
	$sqlQuery = "SELECT COUNT( `qa_team_id` ) as CNT
		FROM `teams` AS qa
		INNER JOIN stores AS s ON qa.`qa_store_id` = s.qa_store_id
		WHERE qa_user_id = ".mysql_escape_string($user_id);	
     $result = $this->db->query($sqlQuery)->result_array();
     $result[0]['CNT'];
     return $result[0]['CNT'];
  }
  function updateTeam($data , $team_id){
    $this->db->where('qa_team_id',$team_id);
    $this->db->update('teams',$data);
  }
  function deleteTeam($qa_team_id){
    //embed_code
    $this->db->query('Delete FROM team_members WHERE qa_team_id='.$qa_team_id);
    $this->db->query('Delete FROM teams WHERE qa_team_id='.$qa_team_id);	
  }
  function getTeamId($store_id)
  {
    $sqlQuery = "SELECT *
		FROM `teams`
		WHERE `qa_store_id` = ".$store_id."
		";
    $result= $this->db->query($sqlQuery)->result_array();    
    return $result[0]['qa_team_id'];
  }
  function checkUserStore($id,$uid)
  {
    $this->db->where('qa_store_id',$id);
    $this->db->where('qa_user_id',$uid);
    $result = $this->db->get('teams')->result_array();
    if($result)
      return true;
    else
      return false;
  }
  function getByStoreId($store_id)
  {
    $this->db->select('qa_team_id');
    $this->db->where('qa_store_id',$store_id);
    return $this->db->get('teams')->result_array();
  }
}
?>
