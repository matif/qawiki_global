<?php

/**
 * 
 * @package - milestone_badges
 * 
 * @author - Kashif
 * 
 */

class milestone_badges extends Model
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
  function get($store_id)
  {
    $this->db->select('b.*, m.name');
    
    $this->db->where('b.store_id', $store_id);
    
    $this->db->join('milestones m', 'b.milestone_id = m.id', 'INNER');
    
    return $this->db->get('milestone_badges b')->result_array();
  }
  
  /**
   * function save
   * 
   * save milestone badge
   * 
   */
  function save($data)
  {
    $this->db->insert('milestone_badges', $data);
  }

  /**
   * function update
   * 
   * update milestone badge
   * 
   */
  function update($badge_id, $data)
  {
    $this->db->where('id', $badge_id);
    
    $this->db->update('milestone_badges', $data);
  }
  
  /**
   * 
   * function getOne
   * 
   * @param <int> $id    id of the badge
   * @param <int> $store_id    id of the store
   * 
   * return single badge information
   * 
   */
  function getOne($id, $store_id)
  {
    $this->db->where('id', $id);
    $this->db->where('store_id', $store_id);
    
    $row = $this->db->get('milestone_badges')->result_array();
    
    return ($row) ? $row[0] : null;
  }
  
  /**
   * 
   * function delete
   * 
   * @param <int> $id    id of the badge
   * 
   * delete the badge
   * 
   */
  function delete($id)
  {
    $this->db->where('id', $id);
    
    $this->db->delete('milestone_badges');
  }
  
  /**
   * 
   * function getBadgesId
   * 
   * return badges ids
   * 
   */
  function getBadgesId($store_id, $badge_id = 0)
  {
    $this->db->select('milestone_id');
    
    $this->db->where('store_id', $store_id);
    $this->db->where('id <> '. $badge_id);
    
    $rows = $this->db->get('milestone_badges')->result_array();
    
    $data = array();
    
    foreach($rows as $key => $row)
    {
      $data[] = $row['milestone_id'];
    }
    
    return $data;
  }
}