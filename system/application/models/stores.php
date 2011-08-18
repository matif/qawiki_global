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
class Stores extends Model {
    //put your code here
  function Stores()
  {
	parent::Model();
  }
  function addStores($data)
  {
    $this->db->insert('stores', $data);
  }
  function getStores($user_id , $offset=0 , $limit=0)
  {
    if($offset==0 && $limit==0) {
        $sqlQuery = "SELECT * FROM stores WHERE qa_user_id = $user_id order by qa_store_id desc";
    } else {
            $sqlQuery = "SELECT * FROM stores WHERE qa_user_id = $user_id order by qa_store_id desc LIMIT $offset, $limit";
    }
    return $this->db->query($sqlQuery)->result();
  }
  function getStoreCount($user_id)
  {
     $sqlQuery = "SELECT COUNT(qs.qa_store_id) as CNT
            FROM `stores` AS qs
            INNER JOIN teams AS qt ON qt.qa_store_id = qs.qa_store_id
            INNER JOIN team_members AS tm ON qt.qa_team_id = tm.qa_team_id
            WHERE tm.qa_user_id = $user_id";
     $result = $this->db->query($sqlQuery)->result_array();
     return $result[0]['CNT'];
  }
  
  function getStoreById($id)
  {
     $sqlQuery = "SELECT * FROM stores WHERE `qa_store_id` = $id";
     $result = $this->db->query($sqlQuery)->result();
     if($result == NULL)
       $result = 0;
     return $result;
  }
  function updateStore($store_id , $data) {
    $this->db->where('qa_store_id',$store_id);
    $this->db->update('stores',$data);
  }

  function deleteStore($store_id){
    //embed_code
    $this->db->query('Delete FROM stores WHERE `qa_store_id`='.$store_id);
    
  }
  function getMemeberStore($id, $offset, $limit, $type = 'user')
  {    
    $sqlQuery = "SELECT qs.qa_store_id,qs.qa_store_name,qs.qa_who_can_comment,tm.role
            FROM `stores` AS qs
            INNER JOIN teams AS qt ON qt.qa_store_id = qs.qa_store_id
            INNER JOIN team_members AS tm ON qt.qa_team_id = tm.qa_team_id
            WHERE tm.qa_user_id = $id LIMIT $offset, $limit";      
    return $this->db->query($sqlQuery)->result_array();
  }


  function getStoresByName($is_admin, $user_id, $name, $type = '')
  {
    if($is_admin)
    {
      $this->db->select('`stores`.`qa_store_name` as store_name, `stores`.`qa_store_id` as Id');
      $this->db->where('moderation_type IN(3,5)');
      $this->db->like('qa_store_name', $name);
      
      return $this->db->get('stores')->result_array();
    }


    $this->db->select('s.`qa_store_name` as store_name, s.`qa_store_id` as Id');
    
    if($type == 'spam')
    {
      $this->db->where('s.moderation_type', 1);
    }
    elseif($type != 'skip')
    {
      $this->db->where('s.moderation_type IN(4,5)');
    }

    $this->db->join('teams t','tm.qa_team_id = t.qa_team_id','inner');
    $this->db->join('stores s','t.qa_store_id = s.qa_store_id','inner');
    $this->db->where('tm.`qa_user_id`',$user_id);
    $this->db->like('s.qa_store_name', $name);
    $this->db->group_by('tm.qa_team_id');

    return $this->db->get('team_members tm')->result_array();
  }


  function checkImagePost($store_id)
  {
    $this->db->select('image_option');
    $this->db->where('qa_store_id', $store_id);
    $result = $this->db->get('stores')->result_array();
    return ($result) ? $result[0]['image_option']:null;
  }
  function getModerationSetting($store_id)
  {
    $this->db->select('moderation_type');
    $this->db->where('qa_store_id',$store_id);
    $result = $this->db->get('stores')->result_array();
    return ($result) ? $result[0]['moderation_type']: null;
  }
  function checkUserStore($id,$uid)
  {
    $this->db->where('qa_store_id',$id);
    $this->db->where('qa_user_id',$uid);
    $result = $this->db->get('stores')->result_array();        
    if($result)
      return true;
    else
      return false;
  }
  function imageOption($store_id)
  {
    $this->db->select('save_images_locally');
    $this->db->where('qa_store_id', $store_id);
    $result  = $this->db->get('stores')->result_array();
    return $result[0]['save_images_locally'];
  }
  function get_ftp_stores($offset, $limit = 10, $store_id = 0)
  {    
    $this->db->select('ftp_file_name, qa_store_id, qa_user_id');
    if($store_id != 0)
      $this->db->where("qa_store_id", $store_id);
    $this->db->where('ftp_file_name <> ""');    
    $this->db->offset($offset);
    $this->db->limit($limit);
    $result = $this->db->get('stores')->result_array();    
    return (isset($result[0]))?$result:NULL;
  }

  function getAllStores($offset,$limit)
  {
//    $this->db->select('*');
    $this->db->limit($limit, $offset);
    return $this->db->get('stores')->result_array();
  }
  function countAllStores()
  {
    $this->db->select('*');
    return $this->db->count_all('stores');
  }
}