<?php

function verify_logged_in_user()
{
  $CI =& get_instance();

  if(!trim($CI->session->userdata("uid")))
  {
    redirect('register');
  }
}

function mk_dir($dir_path, $rights = 0777)
{
  umask(000);
  
  $tokens = explode('/', $dir_path);
  $dir = '';
  foreach ($tokens as $token)
  {
    $dir .= $token;
    if (!is_dir($dir) && strlen($dir) > 0)
    {
      mkdir($dir, $rights);
      chmod($dir, $rights);
    }

    $dir .= '/';
  }
}

/* global utlitly */

function use_javascript($file)
{
  $CI =& get_instance();
  
  $file = trim($file);
  
  if(strpos($file, 'http://') === false)
  {
    $file = base_url().'js/'.$file;
  }
  
  if(substr($file, strlen($file) - 3, 3) != '.js')
  {
    $file .= '.js';
  }
  
  if(!isset($CI->javascript_array))
  {
    $CI->javascript_array = array();
  }
  
  if(!in_array($file, $CI->javascript_array))
  {
    $CI->javascript_array[] = $file;
  }
}

function include_javascript()
{
  $CI =& get_instance();
  
  if(!isset($CI->javascript_array))
    return false;
          
  foreach($CI->javascript_array as $js_file)
  {
    echo '<script type="text/javascript" src="'.$js_file.'"></script>';
  }
}

/********************/

function validate_csv_header($fields, &$data)
{
  $required = array(
    'item id',
	'item type',
    'title',
    'description'
  );

  foreach($required as $value)
  {
    if(!isset($fields[$value]))
    {
      $data['error'][] = $value;
    }
  }

  if(isset($data['error']))
  {
    $data['error'] = 'Following field(s) are missing: '.join(', ', $data['error']).'.';
  }
}

function get_image_path($icon_path, $store_id, $thumb = true)
{
  return trim($icon_path) ? get_store_dir_url($store_id) . ($thumb ? 't-' : '') . $icon_path : base_url() . default_logo_image();
}

function select_tag($name, $data, $selected = '', $config = array(), $disabled = array(), $skip = array(),$default ="off")
{
  $select = '<select name="'.$name.'" id="'.(isset($config['id']) ? $config['id'] : $name).'"
    '.(isset($config['class']) ? 'class="'.$config['class'] .'"' : '').' 
    '.(isset($config['onchange']) ? 'onchange="'.$config['onchange'] .'"' : '').'>';
  if($default == "on") 
  $select .= "<option value= ''>Select Store</option>";
  foreach($data as $key => $value)
  {
    if(in_array($key, $skip)) continue;
    
    $select .= '<option value="'.$key.'" '.($key == $selected ? 'selected="selected"' : '').' '.(in_array($key, $disabled) ? 'disabled="disabled"' : '').'>'.$value.'</option>';
  }

  $select .= '</select>';

  return $select;
}

function get_font_family_list()
{
  return array(
    'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
    'Comic Sans MS'                => 'Comic Sans MS',
    'Georgia'                      => 'Georgia',
    'Tahoma'                       => 'Tahoma',
    'Verdana'                      => 'Verdana'
  );
}

function getInvites($uid)
{
  $CI =& get_instance();

  $CI->db->select('*');
  $CI->db->where('qa_user_id',$uid);
  $CI->db->from('team_invites');
  
  $cnt = $CI->db->count_all_results();  
  return $cnt ;
}

function link_js($file_name)
{
  return '<script type="text/javascript" src="'.base_url().'js/'.$file_name.'.js"></script>';
}

function store_permissions_mapping($permission)
{
  $mapping = array(
    'view_Q&A_only'           => 'view',
    'post_only_questions'     => 'question',
    'post_questions_answers'  => 'both'
  );

  return $mapping[$permission];
}

function render_json_response($data)
{
  if(isset($_REQUEST['jsoncallback']))
  {
    echo $_REQUEST['jsoncallback'].'('.json_encode($data).')';
  }
  else
  {
    echo json_encode($data);
  }

  exit;
}

function may_require_exit($data, $validate)
{
  if(!$validate)
  {
    render_json_response($data);
  }
}

/**
 * Takes a date string and will return, in English, how much time has passed i.e: "2 Days Ago"  Will also return just the integer interval.
 *
 * @param string $date            A date as a string
 * @param boolean $numbersOnly    Default is false, when true returns only the difference as an integer
 * @return mixed                  By default returns the time difference in words.  Returns the time difference as an int if $numbersOnly is true
 */
function date_to_words($date, $numbersOnly = false)
{
  $gmtTime = gmdate('Y-m-d H:i:s');

  $timestamp_diff = strtotime($gmtTime) - strtotime($date);

  $time_interval = '';
  $time_difference = 0;

  if($timestamp_diff < (60 * 60))
  {
    $time_difference = floor($timestamp_diff / 60);
    $time_interval = ' minute(s) ';
  }
  elseif($timestamp_diff < (60 * 60 * 24))
  {
    $time_difference = floor($timestamp_diff / 60 / 60);
    $time_interval = ' hour(s) ';
  }
  elseif($timestamp_diff < (60 * 60 * 24 * 7))
  {
    $time_difference = floor($timestamp_diff / 60 / 60 / 24);
    $time_interval = ' day(s) ';
  }
  elseif($timestamp_diff < (60 * 60 * 24 * 7 * 4))
  {
    $time_difference = floor($timestamp_diff / 60 / 60 / 24 / 7);
    $time_interval = ' week(s) ';
  }
  else
  {
    $total_months = $months = floor($timestamp_diff / 60 / 60 / 24 / 30);

    if($months >= 12)
    {
      $months = ($total_months % 12);
      $years = ($total_months - $months) / 12;
      $time_difference = $years;
      $time_interval = ' year(s) ';
    }

    if($months > 0)
    {
      $time_difference = $months;
      $time_interval = ' month(s) ';
    }
  }

  $text = (($numbersOnly) ? '' : $time_interval . 'ago');

  return $time_difference . $text;
}
function createDir($id , $path)
{  
  $dir = $path;  
  umask(000);  
  if(!file_exists($dir))
    @mkdir($dir, 0777);
  $dir .= $id.'/';  
  if(!file_exists($dir))
    @mkdir($dir, 0777);  
  return $dir;
}

function make_image_file_name($file_name)
{
  $tokens = explode('.', $file_name);
  return time() . '.' . $tokens[count($tokens) - 1];
}

function load_upload_library($upload_path = null)
{
  $CI =& get_instance();

  $config['upload_path'] = (!$upload_path) ? $CI->config->item('root_dir').'/uploads/' : $upload_path;
  $config['allowed_types'] = 'gif|jpg|png|jpeg|pjpg|pjpeg';
  $config['max_size']	= '4096';
  // file uploading
  $CI->load->library('upload', $config);
  
  mk_dir($config['upload_path']);
}

function checkSpams($title, $description)
{    
  $banned_words = array('asshole','blowjob', 'faggot', 'fuck', 'nigga', 'nigger', 'shit',' @', '@$$',' @ss', '@zz',
    'a$$', 'a*s', 'arsehole', 'arvo', 'as*', 'ass', 'ass_boy', 'ass_hole', 'asses',
    'assfuck', 'asshole','assholes', 'asslick', 'asss', 'asswipe', 'aunty', 'azz', 'b!tch', 'b*tch',
    'b*tches', 'b.i.t.c.h','b.i.t.c.h.e.s','b1tches', 'b8tch', 'bastard', 'bastards', 'basterd',
    'basterds', 'bastrd','beat_off', 'biatch', 'biffy', 'biiitc', 'biitch', 'biotch', 'bitch', 'b-itch',
    'bitchass', 'bitchboy','bitches', 'bitchh', 'bitchy', 'bitsch', 'bloody_hell', 'bltch', 'boner',
    'btch', 'bullshit', 'bullshitted','bullshyt', 'bum-bandit', 'buttfuck', 'buttfucker', 'buttfucking',
    'buttpirate', 'bytch', 'c0ck','c0cks', 'carpetmunch*', 'choad', 'clit', 'cock', 'cocks', 'cocksmoker',
    'cocksucker', 'coon', 'cottonpicker', 'cum, cumlicker','cunt', 'cunts', 'd!ck', 'd*ck', 'd_yke', 'dck',
    'dick', 'dick!!', 'dickhead', 'dickheadd', 'dickk','dickwad', 'dike', 'dikes', 'dildo',
    'dingleberry', 'dipshits', 'd--k', 'douche', 'douchebag', 'dumbfuck', 'dushe', 'dyke',
    'dykes', 'eddress', 'exchange.viacom.com', 'f#cking', 'f*ck', 'f*ckin', 'f*cking', 'f.u.c.k', 'f.u.c.k.e.r.',
    'f.ucking', 'f_u_c_k', 'f_u_c_k_e_r', 'fag', 'fagget', 'faggit', 'faggot', 'faggots','fak', 'fcked',
    'fcking', 'fk', 'flange', 'fu(k', 'fu.ck.in', 'fuc', 'fucc', 'fuccin', 'fuccking', 'fuch', 'fuck', 'f-u-c-k', 'fucka', 'fucked', 'fucken',
    'fucker', 'fuckers', 'fuckin', 'fucking', 'f-ucking', 'fuckk', 'fuckka', 'fuckken', 'fuckkers', 'fuckktard',
    'fuckn', 'fuckoff', 'fucks', 'fucktard', 'fucktwit', 'fuckwad', 'fuckwads', 'fuckyou', 'fucl', 'fucx',
    'fuggin', 'fugly', 'fuglyass', 'fuk', 'fukc', 'fukin', 'fukk', 'fukked', 'fukken', 'fukkin', 'fukkkk',
    'fukkkken', 'fuuck', 'fvck', 'fvcking', 'fvkking', 'gangbang', 'goddamn', 'golden_shower', 'h.o.e.s', 'hershey',
    'hodgie', 'homo', 'homos', 'honkey', 'hork', 'jackshit', 'jizzbucket', 'jizzmoppers', 'jizzum', 'kike',
    'lezbos', 'libido', 'littlefag', 'm0therfucker.', 'meatrack', 'motherfcker', 'motherfucker', 'motherfucker.',
    'mtvi', 'mtvi.com', 'mtvicom', 'mtvn', 'mtvn.com', 'mtvncom', 'mtvnmix', 'mtvnmix.com', 'mtvnmixcom', 'mtvstaff',
    'mtvstaff.com', 'mtvstaffcom', 'muff', 'muffdiver', 'muthafugga', 'muthaphuckin', 'mutherfuck', 'n.i.g.g.e.r',
    'n.i.g.g.e.r.', 'n_i_g_g_e_r', 'negro', 'negroid', 'nfuck', 'ni$%as', 'ni99a', 'ni99as', 'nig', 'nigg',
    'nigga', 'niggaz', 'nigger', 'nig-ger', 'n-i-g-g-e-r','niggers', 'niggr', 'niggy', 'nlgga', 'noonan', 'nudger',
    'oldtwat', 'Pecker', 'peckerwood',' phuck', 'phucked', 'phuk', 'phuque', 'pillow_biter', 'pillowbiter',
    'poontang', 'poopchute', 'poopshoot', 'porch_monkey', 'porchmonkey', 'pussies', 'pussy', 'pussyass', 'queef',
    'queer', 'queers', 'reestie', 'rim_job', 'rimjob', 'rump-shaker', 's.h.i.t', 's.h.i.t.t.e.d',
    's_h_i_t', 'sh!t', 'sh1t', 'shiits', 'shit', 'shit_head', 'shite', 'shithead', 'shitheads', 'shitload', 'shitney',
    'shits', 'shitstain', 'shitstains', 'shitt', 'shitting', 'shitty', 'shyt', 'skank', 'slizzut', 'slut',
    's-lut', 'sluts', 'snatch', 'spick', 'spooge', 'stupidfuck', 'stupidfuckk', 't.i.t.s', 'teabag', 'tit', 'tits', 'titties', 'titty',
    'titty_fuck', 'tittyfuck', 'tramp', 'twat', 'uglystinkingazz', 'vh1staff', 'vh1staff.com', 'vh1staffcom',
    'wh0re', 'whhore', 'whoore', 'whore', 'w-hore', 'wigger', 'wuss', 'yasser', 'zipperhead', '69', 'Anal',
    'Anus', 'Arse', 'Ass', 'Asshole', 'Asswipe', 'Asslick', 'Ball/s', 'Bang', 'Barebacking', 'Bastard', 'Bitch',
    'blowjob', 'Bugger', 'Bullshit', 'Bumhole', 'Bumwipe', 'Bumlick', 'Butt', 'Butthole', 'Buttpirate', 'Carpetmunch',
    'Carpet munch', 'Carpetmunching', 'Carpet munching', 'chink', 'Clit', 'Cock' , 'Cockblock', 'Cock block', 'Cocksucker', 'Cock sucker',
    'Cocktease', 'Cock tease', 'Cockteaser',' Cock teaser', 'Cooch', 'Coon', 'Cooter', 'cracker', 'Cum',
    'cunnilingus', 'Cunt', 'Crap', 'Damn', 'Dick', 'Dick Head', 'Dike', 'Dildo', 'Doggystyle', 'Doggystyle', 'Dyke',
    'Ejaculate', 'Erect', 'fag', 'faggit', 'Faggot', 'Fellatio', 'Fart', 'Felch', 'Fisted', 'Fisting', 'Fist', 'Fornicate',
    'Fuck', 'Fucked', 'Fucker', 'Fucking', 'Fudgepacker' , 'Fudge packer' , 'Gangbang', 'Goddamn', 'God damn',
    'Golden Shower', 'GoldenShower', 'Gook', 'Hand Job', 'Handjob', 'Hard On', 'Hardon', 'Head' , 'Hell',
    'Ho', 'Hoe', 'Homo', 'Hummer', 'Jack off', 'Jail Bait', 'jailbait', 'Jill off', 'Jism', 'Jiz', 'Junglebunny',
    'Kike', 'Lesbo', 'Masturbate', 'Mic', 'Molest', 'Mooley', 'Motherfucker', 'Mother fucker', 'Muthafucka', 'Muff', 'Muffdiving',
    'Muff diving', 'Muffdiver', 'Muff diver', 'Nambla', 'Nigga' , 'Nigger', 'Orgy', 'Pecker', 'Pedophile',
    'Penis', 'Piss', 'Poontang', 'Prick', 'Pubic', 'Punani', 'Pussy', 'Queer', 'Rape', 'Rapist', 'Rectal', 'Rectum',
    'Rim', 'Screw', 'Scrotum', 'Seman', 'Shit', 'Sixtynine', 'Sixty nine', 'Snatch' , 'Spic', 'Spick', 'Tit',
    'Tits', 'Towelhead', 'Towel head', 'Turd', 'Twat', 'Vibrator', 'Vigina', 'Wetback', 'Wet back', 'Whore', 'Woody', 'Wop');

//  in_array($needle, $haystackarray);
  $description = explode(" ", $description);
  $title = explode(" ",$title);
  foreach($banned_words as $banned)
  {
    if(in_array($banned,$description)  || in_array($banned,$title) )
    {
      return false;
    }
  }
  
  return true;
}

function shorten_url($url, $action_type = 'widget_load', $store_id = 0, $item_id = 0, $item_type = '', $user_id = 0)
{
  // call the url shortener
  $api_call = 'http://qawiki.iserver.purelogics.info/yourls/yourls-api.php?username=qawiki&password=pladmin&action=shorturl&format=xml';
  $api_call .= '&custom_action=' . $action_type;
  $api_call .= '&store_id=' . $store_id;
  $api_call .= '&url=' . urlencode($url);
  $api_call .= '&item_id=' . $item_id;
  $api_call .= '&item_type=' . $item_type;
  $api_call .= '&user_id=' . $user_id;

  $response = file_get_contents($api_call);

  // convert from xml and return the shortened url
  $simpleXml = simplexml_load_string($response);

  if($simpleXml instanceof SimpleXMLElement)
  {
    $xml = (array) $simpleXml;

    if(is_array($xml) && isset($xml['shorturl']))
    {
      return $xml['shorturl'];
    }
  }

  return false;
}

function generate_short_url($url = '', $action_type = 'widget_load', $store_id = 0, $item_id = 0, $item_type = '', $user_id = 0, $postfix = '', $track_click = true)
{
  if(!trim($url) && isset($_SERVER['HTTP_REFERER']) && trim($_SERVER['HTTP_REFERER']))
  {
    $url = $_SERVER['HTTP_REFERER'].(trim($postfix) ? '#'.$postfix : '');
  }

  if(trim($url))
  {
    $data['short_url'] = shorten_url($url, $action_type, $store_id, $item_id, $item_type, $user_id);

    if(!$track_click)
    {
      return $data['short_url'];
    }
      
    @file_get_contents($data['short_url']);

    return true;
  }

  return false;
}

function functionName($param)
{

}

function trim_text($text, $length = 20, $dots = true)
{
  return (mb_strlen($text) > $length) ? mb_substr($text, 0, $length) . ($dots ? '...' : '') : $text;
}

function render_json_paginated_data($total, $offset, $limit, $data, $callback, $url)
{
  $url = base_url().$url;

  $CI =& get_instance();
  $CI->pager->create($total, $offset, $limit, $callback, $url);

  $data_pager = array(
    'pager' => $this->pager->anchors,
    'results' => $data_view['questions']
  );
  echo json_encode(array($data_pager));
  die();
}

function moderate_params($store_mod_type)
{
  $CI =& get_instance();

  $mod_level = $CI->mod_level;
  $mod_status = $CI->mod_status;

  $query = '';

  if($store_mod_type == 5)
  {
    if($mod_status != 'IS NULL')
    {
      $query .= ' AND q.mod_level = '.$mod_level;
    }
    $query .= ' AND q.mod_status '.($mod_status == 'IS NULL' ? 'IS NULL' : ' = "'.$mod_status.'"');
  }
  elseif($store_mod_type == 3 || $store_mod_type == 4)
  {
    $query .= ' AND (q.mod_status = "" OR q.mod_status IS NULL)';
  }

  return $query;
}

function object_to_array($d) {
  if (is_object($d))
  {
    $d = get_object_vars($d);
  }

  if (is_array($d)) {
    return array_map(__FUNCTION__, $d);
  }
  else
  {
    // Return array
    return $d;
  }
}


function get_filter_text($filter_text)
{
  $CI =& get_instance();

  $filter_text = trim($filter_text);

  if($CI->input->post('filter_text'))
  {
    $filter_text = $CI->input->post('filter_text');
  }
  
  return $filter_text;
}

function format_filter_text($filter_text)
{
  if(trim($filter_text) && $filter_text != -1)
  {
    $filter_text = preg_replace('/\s+/si', ' ', $filter_text);
    $filter_text = mysql_escape_string($filter_text);
    $filter_text = str_replace(' ', '% ', $filter_text).'%';
  }

  return $filter_text;
}

/*----------------------------------------*/
/*--------- Related to new design --------*/
/*----------------------------------------*/

/**
 * 
 * get store list for current user
 * 
 * 
 */
function set_store_list($store, $user_id)
{
  $rows = $store->getMemeberStore($user_id, 0, 100);
  
  $stores = array();
  
  foreach($rows as $row)
  {
    $stores[$row['qa_store_id']] = $row['qa_store_name'];
  }
  
  $CI =& get_instance();
  $CI->stores_list = $stores;
}

/**
 * 
 * set body class
 * 
 * 
 */
function set_body_class()
{
  $CI =& get_instance();
  
  if(in_array($CI->uri->segment(2), array('stores')) || in_array($CI->uri->segment(1), array('account', 'dashboard')))
  {
    $CI->body_class = 'short_header';
  }
  elseif($CI->uri->segment(1) == 'main' && $CI->uri->segment(2) == 'noaccess')
  {
    $CI->body_class = 'short_header';
  }
}

/**
 * 
 * sublinks data
 * 
 * 
 */
function get_sub_links($type)
{
  $sub_links = array();
  
  if($type == 'settings')
  {
    $sub_links = array(array(
        'text'     => 'Moderate',
        'url'      => base_url().'moderate/index/{qa_store_id}',
        'selected' => 'moderate',
        'class'    => 'moderate'
      ), array(
        'text'     => 'Settings',
        'url'      => base_url().'teammembers/index/0/{qa_store_id}',
        'selected' => 'settings',
        'class'    => 'storeSetting'
      ), array(
        'text'     => 'Reports',
        'url'      => base_url().'reports/index/{qa_store_id}',
        'selected' => 'reports',
        'class'    => 'getReports'
      )
    );
  }
  elseif(in_array ($type, array('create_store', 'import_catalog', 'customize_widget')))
  {
    $sub_links = array(array(
        'text'     => 'Settings',
        'url'      => base_url().'post/createStore',
        'selected' => 'settings',
        'class'    => 'storeSetting'
      ), array(
        'text'     => 'Import',
        'url'      => ($type == 'create_store') ? 'javascript:;' : base_url().'post/addPost/{qa_store_id}',
        'selected' => 'catalog',
        'class'    => 'manageCat'
      )
    );
  }
  
  return $sub_links;
}

/**
 * 
 * sublinks data
 * 
 * 
 */
function get_inner_links_array($type)
{
  $sub_links = array();
  
  if($type == 'settings')
  {
    $sub_links = array(array(
        'text'     => 'General',
        'url'      => base_url().'post/createStore/edit/{qa_store_id}',
        'selected' => "settings"
      ), array(
        'text'     => 'Visual',
        'url'      => base_url().'settings/appearance/{qa_store_id}',
        'selected' => 'visual'
      ), array(
        'text'     => 'Catalog',
        'url'      => 'javascript:;',
        'selected' => 'visual'
      ), array(
        'text'     => 'Team',
        'url'      => base_url().'teammembers/index/0/{qa_store_id}',
        'selected' => 'team'
      ), array(
        'text'     => 'Badges',
        'url'      => base_url().'teams/milestoneBadges/{qa_store_id}',
        'selected' => 'badges'
      ), array(
        'text'     => 'Web Ring',
        'url'      => base_url().'teammembers/webRing/{qa_store_id}',
        'selected' => 'visual'
      ),array(
        'text'     => 'Milestones',
        'url'      => base_url().'teams/milestone/{qa_store_id}',
        'selected' => 'milestone'
      ),array(
        'text'     => 'Moderation Groups',
        'url'      => base_url().'teammembers/designations/{qa_store_id}',
        'selected' => 'desigantion'
      ),array(
        'text'     => 'Email Templates',
        'url'      => base_url().'emailTemplates/index/{qa_store_id}',
        'selected' => 'email'
      )
    );
  }
  else if($type == "store")
  {
   $sub_links = array(array(
        'text'     => 'Store Settings',
        'url'      => base_url().'post/createStore/',
        'selected' => 'settigns'
      ), array(
        'text'     => 'Web Settings',
        'url'      => base_url().'post/webInfo/{qa_store_id}/',
        'selected' => 'web_settings'  
      ), array(
        'text'     => 'Upload Products',
        'url'      => base_url().'post/addPost/{qa_store_id}',
        'selected' => 'web_settings'  
      )
    );
  }
  else if($type == "store_edit")
  {
   $sub_links = array(array(
        'text'     => 'Store Settings',
        'url'      => base_url().'post/createStore/edit/{qa_store_id}',
        'selected' => 'settings'
      ), array(
        'text'     => 'Web Settings',
        'url'      => base_url().'post/webInfo/{qa_store_id}/edit',
        'selected' => 'web_settings'
      ), array(
        'text'     => 'Upload Products',
        'url'      => base_url().'post/addPost/{qa_store_id}',
        'selected' => 'upload'
      )
    );
  }
  elseif($type == 'reports')
  {
    $sub_links = array(array(
        'text'     => 'View Report',
        'url'      => base_url().'reports/index/{qa_store_id}'
      ), array(
        'text'     => 'Categories',
        'url'      => base_url().'reports/index/{qa_store_id}/categories'
      ), array(
        'text'     => 'Brands',
        'url'      => base_url().'reports/index/{qa_store_id}/brands'
      ), array(
        'text'     => 'Products',
        'url'      => base_url().'reports/index/{qa_store_id}/products'
      )
    );
  }
  
  return $sub_links;
}

/**
 * 
 * function top_nav_drop_down
 * 
 * @param <string>    $text
 * @param <string>    $drop_down
 * 
 * 
 */
function top_nav_drop_down($text, $drop_down)
{
  $options = '';
  
  if($text == 'Catalog')
  {
    $options = '<span class="custom-dp">
        <span class="custom-dp-item"><a rel="categories" href="javascript:;" '.($drop_down == 'categories' ? 'class="item-sel"' : '').' onclick="location.href=\''.base_url().'catalog/index/{qa_store_id}/categories\'">Categories</a></span>
        <span class="custom-dp-item"><a rel="brands" href="javascript:;" '.($drop_down == 'brands' ? 'class="item-sel"' : '').' onclick="location.href=\''.base_url().'catalog/index/{qa_store_id}/brands\'">Brands</a></span>
        <span class="custom-dp-item"><a rel="products" href="javascript:;" '.($drop_down == 'products' ? 'class="item-sel"' : '').' onclick="location.href=\''.base_url().'catalog/index/{qa_store_id}/products\'">Products</a></span>
      </span>';
  }
  
  return $options;
}
/**
 * 
 * function store_list_redirect_url
 * 
 * make current url dynamic for store list drop down
 * 
 * 
 * return <string> $url
 * 
 */
function store_list_redirect_url()
{
  $CI =& get_instance();
  
  $module = $CI->uri->segment(1);
  $action = $CI->uri->segment(2);
  
  $extra = null;
  
  if(!trim($action))
  {
    $action = 'index';
    $extra = 'index';
  }
  
  $key = $module.'_'.$action;
  
  $mapping = array(
    'post_showProduct'    => 3,
    'reports_index'       => 3,
    'post_createStore'    => 4,
    'moderator_spamPosts' => 3,
    'moderator_index'     => 3,
    'post_postStyle'      => 3,
    'post_addPost'        => 3,
    'settings'            => 3,
    'teammembers'         => 3,
    'teammembers_index'   => 4,
    'teams'               => 3,
    'catalog'             => 3,
    'moderate'            => 3,
    'post_webInfo'        => 3
  );
  
  $url = base_url();
  $main_store_id = 0;

  if(!isset($mapping[$key]) && !isset($mapping[$module]))
  {
    $url .= 'catalog/index/{STORE_ID}';
  }
  else
  {
    $key = (!isset($mapping[$key])) ? $module : $key;
    
    $tokens = $CI->uri->segment_array();
    
    $main_store_id = $CI->uri->segment($mapping[$key]);
    
    if($extra)
    {
      $tokens[] = $extra;
    }
    
    $tokens[$mapping[$key]] = '{STORE_ID}';
    $url .= implode('/', $tokens);
  }
  
  return array($url, $main_store_id);
}

function get_store_path($store_id, $user_id = null)
{
  $CI =& get_instance();

  return $CI->config->item('root_dir').'/VOL/'.(!$user_id ? $CI->uid : $user_id).'/store/'.$store_id.'/';
}

function get_store_dir_url($store_id)
{
  $CI =& get_instance();

  return base_url().'VOL/'.$CI->uid.'/store/'.$store_id.'/';
}

/**
 * Format time for a post
 * 
 * 
 */
function format_time($time)
{
  $time = strtotime($time);
  
  return date('H:i', $time).' PST on '.date('F d, Y');
}

/**
 * function post_status
 * 
 * get the moderation status of a post
 * 
 */
function post_status($post)
{
  $status = 'Approve';
  
  if(trim($post['mod_status']))
  {
    if($post['mod_status'] == 'valid')
    {
      $status = 'Approved';
    }
    else
    {
      $status = 'Rejected';
    }
  }
    
  return $status;
}

/**
 * 
 * function parse_pagination_params
 * 
 * 
 * get params from $_REQUEST
 * 
 */
function parse_pagination_params()
{
  $data = array();
  
  $data['current_page'] = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
  $data['rec_per_page'] = isset($_REQUEST['rec_per_page']) ? $_REQUEST['rec_per_page'] : 5;
  $data['offset'] = ($data['current_page'] - 1) * $data['rec_per_page'];
  
  return $data;
}

/**
 * 
 * function pagination_calculate_pages
 * 
 * @param <array>    $params
 * 
 * calculate the total number of pages
 * 
 */
function pagination_calculate_pages(&$params)
{
  $params['total_pages'] = ceil($params['total_records'] / $params['rec_per_page']);
}

function cmp($a, $b)
{
  return strcmp($a["name"], $b["name"]);
}

function check_image($image_name,$image_array)
{
  $class = "";
  for($i = 0; $i < count($image_array);$i++)
  {
    if($image_name == $image_array[$i])
      $class = "class = selectImage";
  }
  return $class;
}

function get_button_image_url($image_name , $store_id)
{
  $url = "";
  if(trim($image_name))
  {
    
    if(strpos($image_name, 'default') === false)
    {
      $url = get_image_path($image_name, $store_id);
    }
    else
    {
      $url = base_url() . 'images/buttons/' . $image_name;
    }
  }
  return $url;
  
}

/**
 * 
 * function validate_question_functions
 * 
 * 
 */

function validate_question_functions(&$data)
{
  $functions = array(
    'categories',
    'brands',
    'products'
  );
  
  foreach($functions as $function)
  {
    if(!isset($data[$function]))
    {
      $data[$function] = 'on';
    }
  }
}


/***
 * 
 * getDesignations
 * 
 * 
 */

function get_designations($designation)
{
  if(!is_array($designation) || !isset($designation[0]))
  {    
    $designation = array(
        1 => array("designation_name" => "Expert"),
        2 => array("designation_name" => "Manager"),
        3 => array("designation_name" => "Standard")
    );    
  }  
  return $designation;
}

/**
 * 
 * function delete_user_store_directory
 *
 * @param <int>   $store_id
 * @param <int>   $user_id
 * 
 * 
 */
function delete_user_store_directory($store_id, $user_id)
{
  if(!trim($user_id) || !trim($store_id))
    return false;
  
  $path = get_store_path($store_id, $user_id);
  
  if(is_dir($path))
  {
    exec('rm -rf '.$path);
  }
}

function getStoreCount()
{
  $CI =& get_instance();
  $CI->load->model("stores");
  return $CI->stores->getStoreCount($CI->uid);
}

function triger_designation_milestones($store_id)
{  
  
  $data_milestones = array();
  
  $data_milestones[0] =  array(      
    "name"           => "First Answer",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 1,
    "question"       => 0,
    "answer_liked"   => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );  
  
  $data_milestones[1] = array(
    "name"           => "First Question",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 0,
    "question"       => 1,
    "answer_liked"   => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );  
  
  $data_milestones[2] = array(
    "name"           => "First Answer Liked",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 0,
    "answer_liked"   => 1,
    "question"       => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
    
  $data_milestones[3] = array(
    "name"           => "Answer Machine",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 25,
    "question"       => 0,
    "answer_liked"   => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
  
  $data_milestones[4] = array(
    "name"           => "Answer King",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 50,
    "question"       => 0,
    "answer_liked"   => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
  
  $data_milestones[5] = array(
    "name"           => "Answer Supreme (100 answers)",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 100,
    "question"       => 0,
    "answer_liked"   => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
  $data_milestones[6] = array(
    "name"           => "Valued Contributor (5 answers liked)",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 0,
    "answer_liked"   => 5,
    "question"       => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
  $data_milestones[7] = array(
    "name"           => "Key Contributor (20 answers liked)",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 0,
    "answer_liked"   => 25,
    "question"       => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );
  
  $data_milestones[8] = array(
    "name"           => "Community Expert (50 answers liked)",
    "created_at"     => date("Y-m-d, H:m:s"),
    "answer"         => 0,
    "answer_liked"   => 50,
    "question"       => 0,
    "question_liked" => 0,
    "store_id"       => $store_id
  );     
  
  $designation_data [0] = array(
    "designation_name" => "Standard",
    "role"             => "admin",
    "store_id"         =>  $store_id
  );
  
  $designation_data [1] = array(
    "designation_name" => "Manager",
    "role"             => "admin",
    "store_id"         =>  $store_id
  );
  
  $designation_data [2] = array(
    "designation_name" => "Expert",
    "role"             => "admin",
    "store_id"         =>  $store_id
  );
  
  $designation_data [3] = array(
    "designation_name" => "Creator",
    "role"             => "creator",
    "store_id"         =>  $store_id
  );
  
  $creator_role_id = 0;
  $CI =& get_instance(); 
  foreach($data_milestones as $mile)
  {    
    $CI->db->insert('milestones', $mile);     
  }
  
  foreach($designation_data as $des)
  {    
    $CI->db->insert('moderation_groups', $des);     
    if($des["role"] == "creator")
      $creator_role_id = $CI->db->insert_id();
  }
  
  return $creator_role_id;
}

/**
 * 
 * function parse_href_tags
 * 
 * @param <string>     $text
 * 
 */
function parse_href_tags($text)
{
  return preg_replace('/\[(.*?)\|(.*?)\]/si', '<a href="$2">$1</a>', $text);
}