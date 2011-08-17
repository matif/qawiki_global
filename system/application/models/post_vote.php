<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of qa_brand
 *
 * @author kashif
 */
class post_vote extends Model
{
  //put your code here
  function qa_brand()
  {
    parent::Model();
  }

  function has_voted($user_id, $post_id)
  {
    $this->db->select('vote_id');
    $this->db->where('user_id', $user_id);
    $this->db->where('post_id', $post_id);
    $this->db->limit(1);

    $result = $this->db->get('post_vote')->result_array();
    if(!$result || count($result) == 0)
    {
      return false;
    }

    return true;
  }

  function save_vote($data)
  {
    $this->db->insert('post_vote', $data);
    
    $type = ($data['pos_vote'] == 1) ? 'up' : 'down';
    Post_history::saveHistory($data['post_id'], 'vote_'.$type, $data['user_id'], true);
    
    return $this->db->insert_id();
  }

  function get_vote_count(&$questions)
  {
    $ids = array();
    foreach($questions as &$question)
    {
      $ids[] = $question['qa_post_id'];
    }

    if(!empty($ids))
    {
      $this->db->select('post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote');
      $this->db->where_in('post_id', $ids);
      $this->db->group_by('post_id');

      $votes = $this->db->get('post_vote')->result_array();

      foreach($votes as $vote)
      {
        foreach($questions as &$question)
        {
          if($question['qa_post_id'] == $vote['post_id'])
          {
            $question = array_merge($question, $vote);
            break;
          }
        }
      }
    }
  }
}