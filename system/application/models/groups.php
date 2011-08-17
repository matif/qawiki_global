<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of groups
 *
 * @author purelogics
 */
class groups extends Model {
  //put your code here
  function groups()
  {
    parent::Model();
  }
  function addGroup($data)
  {
    $group_id = $this->group_exists($data);

    if(!$group_id)
    {
      $this->db->insert('groups', $data);

      return $this->db->insert_id();
    }

    return $group_id;
  }

  function group_exists($data)
  {
		$this->db->where('qa_user_id', $data['qa_user_id']);
		$this->db->where('qa_name', $data['qa_name']);
    $this->db->limit(1);
    $result = $this->db->get('groups')->result_array();

    if(!$result || count($result) == 0)
    {
      return false;
    }

		return $result[0]['qa_group_id'];
  }

  function getGroups($uid)
  {
    $sqlQuery = "SELECT * FROM groups WHERE qa_user_id = '$uid'";
    return $this->db->query($sqlQuery)->result_array();
  }
  function deleteGroupByID($id)
  {
    $this->db->where('qa_group_id', $id);
    $this->db->delete('groups');
  }
  
}
?>