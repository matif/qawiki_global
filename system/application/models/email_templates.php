<?php

class email_templates extends Model
{
  function email_templates()
  {
    parent::Model();
  }

  function get_all($offset,$store_id = -1,$user_id=null, $limit = 10,$col = "qa_store_id",$order = "asc")
  {
    $this->db->offset($offset);
    $this->db->limit($limit);
    if($store_id != -1)
    {
      $this->db->where('qa_store_id',$store_id);
    }
    $this->db->order_by($col,$order);
    return $this->db->get('user_templates')->result_array();
  }

  function getEmailTempletesByUserId($user_id)
  {
    $this->db->select("id");
	$this->db->select("content");
    $this->db->select("type");
    $this->db->where('qa_user_id',$user_id);    
    return $this->db->get("user_templates")->result_array();
  }

  function email_templete($qa_store_id)
  {    
    $this->db->where("qa_store_id", $qa_store_id);
    $this->db->order_by("id", "asc"); 
    return $this->db->get("user_templates")->result_array();    
  }
  	
  function save($content, $email_type, $store_id = 0, $user_id = 0)
  {
    $this->db->insert('user_templates', array(
      'content'     => $content,
      'type'        => $email_type,
      'qa_store_id' => $store_id,
      'qa_user_id'  => $user_id
    ));
  }

  function update($id, $content, $email_type)
  {
    $this->db->where('id', $id);
    $this->db->update('user_templates', array(
      'content'  => $content,
      'type'     => $email_type
    ));
  }

  function delete($id)
  {
    $this->db->query("DELETE FROM user_templates WHERE id = ".$id);
  }

  function get($id)
  {
    $this->db->select("content");
    $this->db->select("type");
    $this->db->where('id',$id);    
    return $this->db->get("user_templates")->result_array();
  }

  function getTemplateByStoreId($store_id,$user_id)
  {
    $this->db->select("*");
    $this->db->where('qa_store_id',$store_id);
    $this->db->where('qa_user_id',$user_id);
    $this->db->where('type',"thank_you");
    $this->db->order_by("id");
    return $this->db->get("user_templates")->result_array();
  }
}