<?php

function default_logo_image()
{
  return 'images/widget/qa_img.jpg';
}

function default_embed_code ($key = false)
{
  $defaults = array(
    'width'               => 450,
    'height'              => 300,
    'font_family'         => 'Arial,Helvetica,sans-serif',
    'font_color'          => '000000',
    'link_color'          => '147FCA',
    'action_text_color'   => '30019B',
    'icon_path'           => '',
    'voting_option'       => 'default',
    'vote_positive_image' => '',
    'vote_negative_image' => '',
    'popular_tab'         => 'on',
    'recent_tab'          => 'on',
    'unanswered_tab'      => 'on',
    'search_tab'          => 'on',
    'default_button'      => 'qaw-buton-gray',
    'height_opt'          => 'auto'
  );

  return ($key) ? $defaults[$key] : $defaults;
}

function parse_embed_code (&$code)
{
  if(!is_array($code))
  {
    $code = json_decode($code, true);
  }

  $defaults = default_embed_code();

  foreach($defaults as $key => $value)
  {
    if(!isset($code[$key]) || !trim($code[$key]))
    {
      $code[$key] = $value;
    }
  }

  if($code['width'] < $defaults['width'])
    $code['width'] = $defaults['width'];

  if($code['height'] < $defaults['height'])
    $code['height'] = $defaults['height'];
}

function embed_code_css ($store_id)
{
  $css = link_tag('css/widget/widget_new.css');
  $css .= link_tag('css/widget/jquery-ui-custom.css');
  $css .= link_tag(get_store_dir_url($store_id).'widget.css');
  
  /*$css .= '<style type="text/css">';

  $css .= '#qawiki-widget {color: #'.$code['font_color'].'; font-family: '.$code['font_family'].'; width: '.$code['width'].'px}';
  $css .= '#qawiki-widget a, #qawiki-widget a:hover, #qawiki-widget a:visited {color: #'.$code['link_color'].'}';
  $css .= '.qawiki-blue-box .qa-center {width: '.($code['width'] - 6).'px}';
  $css .= '.qawiki-white-box {width: '.($code['width'] - 28).'px}';
  //$css .= '.qawiki-content-right {width: '.($code['width'] - 202).'px}';

  $css .= '</style>';*/

  return $css;
}

function make_embed_code ($store, $sub_id, $type, $member_id, $encode = false)
{
  $params = '';
  
  $embed_code = embed_code_css($store->qa_store_id);
  
  if(!$encode)
  {
    $embed_code .= '<script type="text/javascript">';
    $embed_code .= 'var qawiki_owner_id = "'.$member_id.'";';
    $embed_code .= 'var qawiki_store_id = "'.$store->qa_store_id.'";';
    $embed_code .= 'var qawiki_id = "'.(trim($sub_id) ? $sub_id : '{ID}').'";';
    $embed_code .= 'var qawiki_type = "'.(trim($sub_id) ? $type : '{type}').'";';
    $embed_code .= get_customer_configuration($store);
    $embed_code .= '</script>';
  }
  else
  {
    $params = 'oid='.$member_id.'&sid='.$store->qa_store_id.'&id='.(trim($sub_id) ? $sub_id : '{ID}').'&t='.(trim($sub_id) ? $type : '{type}');
    $params = '?p='.base64_encode($params).'&u='.get_customer_id_param($store);
    if($store->qa_who_can_comment == 2)
    {
      $params .= '&e='.get_customer_email_param($store);
    }
  }
  
  $embed_code .= '<script type="text/javascript" src="'.base_url().'js/jquery-1.4.2.min.js"></script>';
  $embed_code .= '<script type="text/javascript" src="'.base_url().'js/jquery-ui-min.js"></script>';
  $embed_code .= '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e390bec342bbac9"></script>';
  $embed_code .= '<script type="text/javascript" src="'.base_url().'js/widget/widget_new.js'.$params.'"></script>';

  return $embed_code;
}

function get_customer_configuration($store)
{
  $embed_code = 'var qawiki_customer_id = "';
  
  $embed_code .= get_customer_id_param($store);
    
  $embed_code .= '";';
  
  if($store->qa_who_can_comment == 2)
  {
    $embed_code .= 'var qawiki_customer_email = "';
    
    $embed_code .= get_customer_email_param($store);
    
    $embed_code .= '";';
  }
  
  
  return $embed_code;
}

/**
 * 
 * function get_customer_id_param
 * 
 * @param <object>    $store
 * 
 */
function get_customer_id_param($store)
{
  $embed_code = '{CUSTOMER_ID}';
  
  if($store->cart_type == 'oscommerce')
  {
    $embed_code = '<?php echo (tep_session_is_registered(\'customer_id\'))?$_SESSION[\'customer_id\']:"";?>';
  }
  elseif($store->cart_type == 'magento')
  {
    $embed_code = '<?php echo (Mage::getSingleton(\'customer/session\')->isLoggedIn()) ? Mage::getSingleton(\'customer/session\')->getCustomer()->getId() : ""; ?>';
  }
  elseif($store->cart_type == 'prestashop')
  {
    $embed_code = '{if $cookie->logged}{$cookie->id_customer}{/if}';
  }
  elseif($store->cart_type == 'zencart')
  {
    $embed_code = '<?php echo (isset($_SESSION[\'customer_id\'])) ? $_SESSION["customer_id"] : ""?>';
  }
  elseif($store->cart_type == 'opencart')
  {
    $embed_code = '<?php echo (isset($this->customer->isLogged())) ? $this->customer->getId() : ""?>';
  }
  elseif($store->cart_type == 'joomla')
  {
    $embed_code = '<?php $qawiki_user =& JFactory::getUser(); echo (!$qawiki_user->guest ? $qawiki_user->id : "")?>';
  }
  elseif($store->cart_type == 'wordpress')
  {
    $embed_code = '<?php echo ($current_user ? $current_user->ID : "")?>';
  }
  elseif($store->cart_type == 'drupal')
  {
    $embed_code = '<?php echo ($user->uid ? $user->uid : "")?>';
  }
  
  return $embed_code;
}

/**
 * 
 * function get_customer_email_param
 * 
 * @param <object>    $store
 * 
 */
function get_customer_email_param($store)
{
  $embed_code = '';

  if($store->qa_who_can_comment == 2)
  {
    $embed_code = '{CUSTOMER_EMAIL}';

    if($store->cart_type == 'oscommerce')
    {
      $embed_code = '<?php echo (tep_session_is_registered(\'customer_id\'))?$_SESSION["customer_email_address"]:"";?>';
    }
    elseif($store->cart_type == 'magento')
    {
      $embed_code = '<?php echo (Mage::getSingleton(\'customer/session\')->isLoggedIn()) ? Mage::getSingleton(\'customer/session\')->getCustomer()->getEmail() : ""; ?>';
    }
    elseif($store->cart_type == 'prestashop')
    {
      $embed_code = '{if $cookie->logged}{$cookie->email}{/if}';
    }
    elseif($store->cart_type == 'zencart')
    {
      $embed_code = '<?php echo (isset($_SESSION[\'customer_id\'])) ? $_SESSION[\'customer_email\'] : ""?>';
    }
    elseif($store->cart_type == 'opencart')
    {
      $embed_code = '<?php echo (isset($this->customer->isLogged())) ? $this->customer->getEmail() : ""?>';
    }
    elseif($store->cart_type == 'joomla')
    {
      $embed_code = '<?php echo (!$qawiki_user->guest ? $qawiki_user->email : "")?>';
    }
    elseif($store->cart_type == 'wordpress')
    {
      $embed_code = '<?php echo ($current_user ? $current_user->user_email : "")?>';
    }
    elseif($store->cart_type == 'drupal')
    {
      $embed_code = '<?php echo ($user->uid ? $user->mail : "")?>';
    }
  }

  return $embed_code;
}

function format_post_time(&$posts)
{
  foreach($posts as &$post)
  {
    $post['qa_created_at'] = date_to_words($post['qa_created_at']);
  }
}

function save_post_image($item_id, $file_name)
{
  $CI =& get_instance();
  
  $image_name = '';
  
  if (trim($_FILES[$file_name]['name']))
  {
    // create directory
    $base_path = $CI->config->item('root_dir') . '/uploads/stores/';
    
    $base_path = createDir($item_id, $base_path);

    // load library
    load_upload_library($base_path);

    $CI->upload->file_name = make_image_file_name($_FILES[$file_name]['name']);

    // upload image
    if ($CI->upload->do_upload($file_name))
    {
      $image_name = $CI->upload->file_name;

      $CI->load->helper('image');
      
      resize_image($base_path . $image_name, $base_path . 't-' . $image_name, 100, 100);
    }
  }
  
  return $image_name;
}

function parse_encoded_params()
{
  $params = array();
  if(isset($_REQUEST['p']) && trim($_REQUEST['p']))
  {
    $qstr = base64_decode(trim($_REQUEST['p']));
    parse_str($qstr, $parsed_arr);
    
    $params['team_member_id'] = $parsed_arr['oid'];
    $params['store_id'] = $parsed_arr['sid'];
    $params['ref_id'] = $parsed_arr['id'];
    $params['ref_type'] = $parsed_arr['t'];
  }
  
  if(isset($_REQUEST['u']) && trim($_REQUEST['u']))
  {
    $params['widget_user_id'] = trim($_REQUEST['u']);
  }
  
  return $params;
}

/**
 * 
 * function parse_question_tags
 * 
 * @param <array>       $row
 * @param <string>      $str
 * 
 */
function parse_question_tags($row, $str)
{
  $str = strip_tags($str);
  $str = str_replace('#Date/Time', format_time($row['qa_created_at']), $str);
  
  return $str;
}

/**
 * 
 * function parse_visual_tags
 * 
 * @param <array>       $row
 * @param <string>      $field
 * @param <array>       $tags
 * 
 * return parsed string
 * 
 */
function parse_visual_tags($row, $field, $tags)
{    
  $tokens = explode('|', $field);
  
  foreach($tokens as $token)
  {
    if(array_key_exists($token, $row))
    {
      $row = $row[$token];
    }
  }

  if(!is_array($row))
  {
    $row = strip_tags($row);
    
    foreach($tags as $tag => $value)
    {
      $row = str_replace($tag, $value, $row);
    }
  }
  
  return $row;
}

/**
 * 
 * function check_for_recent_tab_contents
 * 
 * @param <object>       $CI
 * @param <array>        $widget_data
 * @param <string>       $filter
 * 
 * return parsed string
 * 
 */
function check_for_recent_tab_contents($CI, $widget_data, $filter)
{
  if($filter == 'recent')
  {
    require_once APPPATH . 'config/custom/settings.php';
    
    $config = $CI->widget_configuration->getOne($widget_data['team_member_id'], 'appearance', true);
    
    if(isset($config['functions']['sub_title']))
    {
      $filter = $config['functions']['sub_title'];
    }
    else
    {
      $values = array_values($CI->custom_config['recent_tag_contents']);
      $filter = $values[0];
    }
    
    if(isset(Posts::$recent_tab_content[$filter]))
    {
      $CI->config->set_item('WIDGET_QUESTION_PER_PAGE', Posts::$recent_tab_content[$filter][0]);
    }
  }
  
  return $filter;
}

