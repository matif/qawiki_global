<?php

/**
 * class widget_users
 *
 * @author kashif
 */

class widget_users extends Model
{
  function widget_users()
  {
    parent::Model();
  }

  function add_widget_user($user_id, $ref_id, $ref_type)
  {
    if(!$this->already_exists($user_id, $ref_id, $ref_type))
    {
      $this->db->insert('widget_users', array(
        'user_id'       =>  $user_id,
        'ref_id'        =>  $ref_id,
        'ref_type'      =>  $ref_type,
        'last_visited'  =>  gmdate('Y-m-d H:i:s')
      ));
    }
  }

  function already_exists($user_id, $ref_id, $ref_type)
  {
    $this->db->where('user_id', $user_id);
    $this->db->where('ref_id', $ref_id);
    $this->db->where('ref_type', $ref_type);
    $result = $this->db->get('widget_users')->result_array();

    if(!$result || count($result) == 0)
    {
      return false;
    }

    return true;
  }

  function get_count($ref_id, $ref_type)
  {
    $this->db->select('count(*) as cnt');
    $this->db->where('ref_id', $ref_id);
    $this->db->where('ref_type', $ref_type);
    $result = $this->db->get('widget_users')->result_array();

    if($result)
    {
      return $result[0]['cnt'];
    }

    return 0;
  }
  
  function get_top_contributors(&$answers, $ref_id, $ref_type, $contibutors)
  {
    if(!trim($contibutors))
    {
      $contibutors = $this->config->item('top_contributors_min_answer');
    }
    
    $this->db->select('u.qa_user_id, (u.total_answers + u.thumbs_up - u.thumbs_down) as rating');

    $this->db->where_in('w.ref_id', $ref_id);
    $this->db->where('w.ref_type', $ref_type);

    $this->db->join('qa_user u', 'u.qa_user_id = w.user_id AND u.total_answers >= '.$contibutors);

    $this->db->orderby('rating desc');
    $this->db->limit($this->config->item('top_contributors_limit'));

    $results = $this->db->get('widget_users w')->result_array();

    if($results)
    {
      foreach($results as $result)
      {
        foreach($answers as &$answer)
        {
          if($result['qa_user_id'] == $answer['qa_user_id'])
          {
            $answer['top_contributor'] = 1;
            break;
          }
        }
      }
    }

    //echo $this->db->last_query();
    //print_r($results);
  }
}