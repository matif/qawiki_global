<?php

class email_queue extends Model
{
  function  __construct()
  {
    parent::Model();
  }

  function save($ref_id, $type, $url)
  {
    $this->db->insert('email_queue', array(
      'ref_id'      => $ref_id,
      'type'        => $type,
      'url'         => $url,
      'created_at'  => gmdate('Y-m-d H:i:s')
    ));
  }

  function process()
  {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    
    $offset = 0;
    $limit = 100;

    $CI =& get_instance();
    $CI->load->model('email_templates');
    $CI->load->model('qa_teams');
    $CI->load->library('qawidget');

    $default_templates = $CI->email_templates->get_all(0, 0, 100);
    $default_templates = $this->arrange_templates($default_templates);

    do
    {
      $this->db->limit($limit);
      $this->db->offset($offset);
      $queue = $this->db->get('email_queue')->result_array();

      if($queue)
      {
        foreach($queue as $key => $value)
        {
          $func_name = 'get_'.$value['type'].'_data';
          $data = $this->$func_name($value);
          if($data)
          {
            $this->get_item_info($data);
            
            $templates = $CI->email_templates->get_all(0, $data['qa_store_id'], 100);
            $templates = $this->arrange_templates($templates);
            
            foreach($default_templates as $key => $val)
            {
              if(!isset($templates[$key]))
                $templates[$key] = $default_templates[$key];
            }
            
            echo '<pre>';
            print_r($data);
            echo '</pre>';

            $email_text = $this->format_email($templates, $data, $value['url'], $value['type']);
            
            if(trim($email_text) && trim($data['email']))
            {
              $subject = $this->get_subject($value['type']);
              mail($data['email'], $subject, $email_text, "Content-Type: text/html");

              $this->send_email_to_team($data, $value['type']);

              echo '<strong>Email sent to: '.$data['email'].'</strong>';

              $this->delete_from_queue($value['id']);
            }
          }
        }
      }

      $offset += 100;
    }
    while($queue || count($queue) > 0);
  }

  function get_vote_data($row)
  {
    $this->db->select('v.pos_vote, v.neg_vote, p.qa_title, p.qa_description, p.qa_ref_id, p.qa_post_type, p.qa_post_id, u.name, u.email, u.qa_user_id');
    $this->db->join('store_item_posts p', 'v.post_id = p.qa_post_id');
    $this->db->join('users u', 'p.qa_user_id = u.qa_user_id');
    $this->db->where('vote_id', $row['ref_id']);
    $this->db->where('p.mod_status', "valid");

    $result = $this->db->get('post_vote v')->result_array();

    return ($result) ? $result[0] : null;
  }

  function get_answer_data($row)
  {
    $this->db->select('p.qa_ref_id, p.qa_parent_id, p.qa_post_type, p.qa_post_id, s.qa_title, s.qa_description, u.name, u.email, u.qa_user_id');
    $this->db->join('store_item_posts s', 'p.qa_parent_id = s.qa_post_id AND s.email_opt_in = 1');
    $this->db->join('users u', 's.qa_user_id = u.qa_user_id');
    $this->db->where('p.qa_post_id', $row['ref_id']);
    $this->db->where('p.mod_status', "valid");

    $result = $this->db->get('store_item_posts p')->result_array();

    return ($result) ? $result[0] : null;
  }

  function get_answer_approved_data($row)
  {
    $this->db->select('p.qa_ref_id, p.qa_parent_id, p.qa_post_type, p.qa_post_id, s.qa_title, s.qa_description, u.name, u.email, u.qa_user_id');
    $this->db->join('store_item_posts s', 'p.qa_parent_id = s.qa_post_id');
    $this->db->join('users u', 'p.qa_user_id = u.qa_user_id');
    $this->db->where('p.qa_post_id', $row['ref_id']);
    $this->db->where('p.qa_parent_id > 0');
    $this->db->where('p.mod_status', "valid");

    $result = $this->db->get('store_item_posts p')->result_array();

    return ($result) ? $result[0] : null;
  }

  function get_question_approved_data($row)
  {
    $this->db->select('p.qa_ref_id, p.qa_parent_id, p.qa_post_type, p.qa_post_id, p.qa_title, p.qa_description, u.name, u.email, u.qa_user_id');
    $this->db->join('users u', 'p.qa_user_id = u.qa_user_id');
    $this->db->where('p.qa_post_id', $row['ref_id']);
    $this->db->where('p.mod_status', "valid");

    $result = $this->db->get('store_item_posts p')->result_array();

    return ($result) ? $result[0] : null;
  }

  function get__data($row)
  {
    $this->db->select('p.qa_ref_id, p.qa_post_type, p.qa_post_id, s.qa_title, s.qa_description, u.name, u.email, u.qa_user_id');
    $this->db->join('store_item_posts s', 'p.qa_parent_id = s.qa_post_id');
    $this->db->join('users u', 's.qa_user_id = u.qa_user_id');
    $this->db->where('p.qa_post_id', $row['ref_id']);
    $this->db->where('p.mod_status', "valid");

    $result = $this->db->get('store_item_posts p')->result_array();

    return $result[0];
  }

  function get_item_info(&$data)
  {
    $data['qa_post_type'] = strtolower($data['qa_post_type']);
    
    $table = 'qa_'.$data['qa_post_type'];
    $field = 'qa_'.$data['qa_post_type'].'_'.($data['qa_post_type'] == 'product' ? 'title' : 'name');

    $this->db->select($field.' as title, qa_store_id');
    $this->db->where('id', $data['qa_ref_id']);
    $row = $this->db->get($table)->result_array();

    $data['item_title'] = ($row) ? $row[0]['title'] : '';
    $data['qa_store_id'] = ($row) ? $row[0]['qa_store_id'] : '';
  }

  function arrange_templates($templates)
  {
    $data = array();

    foreach($templates as $template)
    {
      $data[$template['type']] = $template;
    }

    return $data;
  }

  function format_email($templates, $data, $url, $type)
  {
    $email = '';
    if(isset($templates[$type]) && trim($templates[$type]['content']))
    {
      $post_id = (isset($data['qa_parent_id']) && $data['qa_parent_id'] > 0) ? $data['qa_parent_id'] : $data['qa_post_id'];
      
      $url = $this->filter_query_string($url, $post_id);
      $turn_off_link = $this->get_turn_off_email_link($data, $url);
      
      $url = generate_short_url($url.'#email', 'email', $data['qa_store_id'], $data['qa_ref_id'], $data['qa_post_type'], $data['qa_user_id'], '', false);
      
      $search = array('{QUESTION}', '{USER_NAME}', '{ITEM_TITLE}', '{URL}', '{BASE_URL}', '{TURN_OFF_NOTIF}');
      $replace = array($data['qa_title'], $data['name'], $data['item_title'], $url, base_url(), $turn_off_link);

      $email = str_replace($search, $replace, $templates[$type]['content']);
    }

    return $email;
  }
  
  function filter_query_string($url, $post_id)
  {
    $expression = array('/\?qa_post_id=.*?/si', '/\&qa_post_id=.*?/si');
    $replace = array('', '');
    
    $url = preg_replace($expression, $replace, $url);
    
    if(trim($url))
    {
      $tokens = parse_url($url);
      if(trim($tokens['query']))
      {
        $url .= '&';
      }
      else
      {
        $url .= '?';
      }
      
      $url .= 'qa_post_id='.$post_id;
    }
    
    
    return $url;
  }

  function get_subject($type)
  {
    if($type == 'answer')
    {
      return 'You question has been answered';
    }
    elseif($type == 'answer_approved')
    {
      return 'You answer has been approved';
    }
    elseif($type == 'question_approved')
    {
      return 'You question has been approved';
    }

    return '';
  }

  function send_email_to_team($data, $type)
  {
    if(!trim($data['qa_store_id']))
      return false;

    $CI =& get_instance();
    $teamId = $CI->qa_teams->getTeamId($data['qa_store_id']);

    if(!trim($teamId))
      return false;

    if($type == 'vote')
    {
      $CI->qawidget->send_vote_email_to_team($teamId, $data['qa_user_id']);
    }
    elseif($type == 'answer')
    {
      // send email to all team members, who have enabled "notify me onocmment"
      $CI->qawidget->send_comment_email_to_team($teamId, $data);
    }
  }
  function set_email_queue($data)
  {
    $this->db->insert('email_queue', $data);
  }
  function delete_from_queue($queue_id)
  {
    $this->db->where('id', $queue_id);
    $this->db->delete('email_queue');
  }
  
  function get_turn_off_email_link($data, $url)
  {
    if(isset($data['qa_parent_id']))
    {
      $params = $data['qa_ref_id'].':|:'.$data['qa_post_type'].':|:'.$data['qa_parent_id'].':|:'.$data['qa_user_id'];
      $params = base64_encode($params);
    
      return base_url().'trigger/turnOffEmail?tk='.$params.'&next='.  urlencode($url);
    }
    
    return '';
  }
}
