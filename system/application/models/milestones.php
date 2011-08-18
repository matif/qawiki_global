<?php

/**
 * 
 * @package - milestones
 * 
 * @author - Kashif
 * 
 */

class milestones extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  /**
   * 
   * function getAll
   * 
   * return list of all milestones
   * 
   */
  function getAll()
  {
    return $this->db->get('milestones')->result_array();
  }
  
  /**
   * 
   * function milestoneBadges
   * 
   * format list of milestones
   * 
   */
  function format($milestones)
  {
    $data = array();
    
    foreach($milestones as $milestone)
    {
      $data[$milestone['id']] = $milestone['name'];
    }
    
    return $data;
  }
  
  function save($data)
  {
    $this->db->insert('milestones', $data);
  }
  
  function store_milestones($store_id)
  {    
    $this->db->where("store_id", $store_id);
    $this->db->order_by("name", "asc"); 
    return $this->db->get("milestones")->result_array();    
  }
  
  function getMilestone($milestone_id, $store_id)
  { 
    $this->db->where("id", $milestone_id);
    $this->db->where("store_id", $store_id);    
    
    return $this->db->get("milestones")->result_array();
  }
  
  function checkPredefinedMilestone($id)
  {
    $this->db->select("name");    
    $this->db->where("id",$id);    
    return $this->db->get("milestones")->result_array();
  }
  
  function update_milestone($milestone_id, $data)
  {
    $this->db->where("id", $milestone_id);
    $this->db->update("milestones",$data);
  }  
  
  function deleteMilestone($milestone_id)
  {
    $this->db->where("id", $milestone_id);
    $this->db->delete("milestones");
  }
  
  function isExists($name, $store_id)
  {
    $this->db->select("name");
    $this->db->where("store_id",$store_id);    
    $this->db->where("name",$name);
    $results = $this->db->get("milestones")->result_array();  
    if(isset($results[0]["name"]))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  function countMileStone($store_id)
  {
    $this->db->where("store_id", $store_id);
    $this->db->from("milestones");
    return $this->db->count_all_results();
  }
}
