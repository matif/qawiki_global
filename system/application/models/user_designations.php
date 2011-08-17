<?php

/**
* Description of user_designations
*
* @author purelogics
*/
class user_designations extends Model
{
  static $tableName = 'moderation_groups';
  
  //put your code here
  function user_designations()
  {
    parent::Model();
  }
  
  /**
   * 
   * function get
   * 
   * @param <int>   $store_id
   * 
   */
  function get($store_id)
  {
    $this->db->where("store_id", $store_id);
    $this->db->order_by("designation_name","asc");
    return $this->db->get(self::$tableName)->result_array();
  }
  
  /**
   * 
   * function get_designation_name
   * 
   * @param <int>   $store_id
   * 
   */
  function get_designation_name($store_id)
  {
    $this->db->select("designation_name");
    $this->db->select("id");
    $this->db->where("store_id", $store_id);
    
    return $this->db->get(self::$tableName)->result_array();
  }
  
  /**
   * 
   * function getById
   * 
   * @param <int>   $store_id
   * @param <int>   $designation_id
   * 
   */
  function getById($store_id, $designation_id)
  {
    $this->db->where("id", $designation_id);
    $this->db->where("store_id", $store_id);
    
    $designation = $this->db->get(self::$tableName)->result_array();
    
    return ($designation) ? $designation[0] : null;
  }
  
  /**
   * 
   * function get_user_desigantins
   * 
   * @param <int>   $store_id
   * @param <int>   $user_id
   * 
   */
  function get_user_desigantins($store_id, $user_id)
  {
    $this->db->select("designation_name");
    
    $this->db->where("store_id", $store_id);
    $this->db->where("user_id", $store_id);
    
    return $this->db->get(self::$tableName)->result_array();
  }
  
  /**
   * 
   * function save
   * 
   * @param <array>   $data
   * 
   */
  function save($data)
  {
    $this->db->insert(self::$tableName, $data);
  }
  
  /**
   * 
   * function already_created
   * 
   * @param <int>      $user_id
   * @param <int>      $store_id
   * @param <srting>   $designation
   * 
   */
  function already_created($user_id, $store_id, $designation)
  {
    $this->db->select("designation_name");
    
    //$this->db->where("user_id",$user_id);
    $this->db->where("store_id", $store_id);
    $this->db->where("designation_name", $designation);
    
    $result = $this->db->get(self::$tableName)->result_array();
    
    if(isset($result[0]["designation_name"]))
    {
      return true;
    }

    return false;
  }
  
  /**
   * 
   * function delete
   * 
   * @param <int>      $store_id
   * @param <int>      $designation_id
   * 
   */
  function delete($store_id, $designation_id)
  {    
    $this->db->where("store_id", $store_id);
    $this->db->where("id", $designation_id);
    
    $result = $this->db->delete(self::$tableName);
  }
  
  /**
   * 
   * function update
   * 
   * @param <int>      $store_id
   * @param <int>      $designation_id
   * @param <array>    $data
   * 
   */
  function update($store_id, $designation_id, $data)
  {    
    $this->db->where("store_id", $store_id);
    $this->db->where("id", $designation_id);
    
    $result = $this->db->update(self::$tableName, $data);
  }
}