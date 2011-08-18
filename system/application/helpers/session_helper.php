<?php

function widget_session_by_referer($user_id, $user_email)
{
  $CI =& get_instance();

  $domain = get_referer_domain();

  if(trim($domain))
  {
    $data = $CI->session->userdata($domain);
    if($data && $data['user_id'] == $user_id)
    {
      return $data;
    }

    $user = $CI->users->getWidgetUserById($user_id, $user_email, $domain);

    $CI->session->set_userdata($domain, $user);

    return $user;
  }
}

function get_referer_domain()
{
  may_require_exit('You silly hacker1!!!', (isset($_SERVER['HTTP_REFERER']) && trim($_SERVER['HTTP_REFERER'])));

  $url = trim($_SERVER['HTTP_REFERER']);
  $url = str_replace(array('http://', 'https://'), array('', ''), $url);

  if(strpos($url, '/') !== false)
  {
    $url = substr($url, 0, strpos($url, '/'));
  }

  return $url;
}

function generate_session_key($token)
{
  $hours = date('H', time());
  $hours = $hours - ($hours % 4);

  $token .= ':|:' . date('Y-m-d').$hours;

  return md5($token);
}

function instantiate_widget_session($team_id, $store_id, $ref_id, $ref_type)
{
  $token = "$team_id:|:$store_id:|:$ref_id:|:$ref_type";
  $session_key = generate_session_key($token);

  $data = array(
    'team_member_id'  => $team_id,
    'store_id'        => $store_id,
    'ref_id'          => $ref_id,
    'ref_type'        => $ref_type
  );

  $CI =& get_instance();
  $CI->session->set_userdata($session_key, $data);

  return $session_key;
}

function session_get_widget_user($field = '')
{
  $domain = get_referer_domain();

  if(trim($domain))
  {
    $CI =& get_instance();
    $data = $CI->session->userdata($domain);
    
    if($data)
    {
      return (trim($field)) ? $data[$field] : $data;
    }

    return (trim($field)) ? '' : array();
  }
}

function session_set_widget_user($field, $value)
{
  $domain = get_referer_domain();

  if(trim($domain))
  {
    $CI =& get_instance();
    $data = $CI->session->userdata($domain);

    $data[$field] = $value;

    $CI->session->set_userdata($domain, $data);
  }
}

function session_set_widget_data($session_key, $field, $value)
{
  $CI =& get_instance();
  $data = $CI->session->userdata($session_key);

  $data[$field] = $value;

  $CI->session->set_userdata($session_key, $data);
}

function session_get_widget_data($session_key)
{
  $CI =& get_instance();

  return $CI->session->userdata($session_key);
}

function get_complete_widget_session($session_key)
{
  $widget_user = session_get_widget_user();
  may_require_exit('You silly hacker2!!!', $widget_user);

  $widget_data = session_get_widget_data($session_key);
  may_require_exit('You silly hacker3!!!', $widget_data);

  return array_merge($widget_user, $widget_data);
}