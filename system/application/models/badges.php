<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of badges
 *
 * @author purelogics
 */
class badges extends Model {
    //put your code here
  function badges()
  {
    parent::Model();
  }
  function addBadges($data)
  {
    $this->db->insert('badges',$data);
  }
  function getBadges($team_id)
  {
    $this->db->select('image_url');
    $this->db->select('qa_team_id');
    $this->db->where('qa_team_id', $team_id);
    return $this->db->get('badges')->result_array();
  }
}
?>
