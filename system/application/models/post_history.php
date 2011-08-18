<?php

/**
 * 
 * @package  -  Post_history
 * 
 * @author  -  Kashif
 * 
 */

class Post_history extends Model
{
  private static $messages = array(
    'vote_up'     =>  'Vote up by %USER%',
    'vote_down'   =>  'Vote down by %USER%',
    'question'    =>  'Question asked by %USER%',
    'moderate'    =>  '%MOD_STATUS% by Moderator %USER%',
    'answer'      =>  'Question answered by %USER%'
  );
  
  function __construct()
  {
    parent::Model();
  }
  
  function save($data)
  {
    $CI =& get_instance();
    
    $CI->db->insert('post_history', $data);
  }
  
  public static function saveHistory($post_id, $action_type, $user, $fetch_user = false,  $action = '')
  {    
    $data = array(
      'post_id'      =>  $post_id,
      'action_type'  =>  $action_type,
      'created_at'   =>  gmdate('Y-m-d H:i:s')
    );

    if($fetch_user)
    {
      $CI =& get_instance();
      
      $user = $CI->users->getUserInfo($user);
      if($user)
      {
        $user = $user['name'];
      }
    }
    
    $message = self::$messages[$action_type];
    $message = str_replace('%USER%', $user, $message);
    $message = str_replace('%MOD_STATUS%', $action, $message);

    $data['message'] = $message;
    
    self::save($data);
  }
  
  function get($post_id, $offset = 0, $limit = 20)
  {
    $this->db->where('post_id', $post_id);
    
    $this->db->offset($offset);
    $this->db->limit($limit);
    
    return $this->db->get('post_history')->result_array();
  }
}
