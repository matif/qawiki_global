<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of post_spam
 *
 * @author purelogics
 */
class post_spam extends Model
{
    //put your code here
  function post_spam()
  {
    parent::Model();
  }
  function getSpamHistory($post_id,$offset = 0)
  {
    $this->db->select('user_id');
    $this->db->select('description');
    $this->db->select('created_at');
    $this->db->where('post_id',$post_id);
    $this->db->limit(10,$offset);
    return $this->db->get('post_spam')->result_array();
  }

  function getSpamHistoryCount($post_id)
  {
    $this->db->select('id');
    $this->db->where('post_id',$post_id);    
    return $this->db->count_all();
  }
  function deleteSpams($post_id)
  {
    $this->db->where('post_id', $post_id);
    $this->db->delete('post_spam');
  }
}
