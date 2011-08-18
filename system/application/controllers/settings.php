<?php

/**
 * 
 * @package    - Settings
 * 
 * @author     - Kashif
 * 
 */

class Settings extends qaController
{

  function __construct()
  {
    parent::__construct();

    $this->load->model('post_products', 'linked_products');

    $this->load->model('widget_configuration');
    $this->load->model('milestones', "milestones");

    $this->load->helper('widget');
    $this->load->helper('image_helper');

    // parse store id param
    $this->store_id = $this->uri->segment(3);
    $user_role = Permissions::can_edit($this->store_id, $this->uid);


    if ($user_role == 'view')
    {
      redirect('catalog/index/' . $this->store_id);
    }

    // get store data
    $this->store_data = $this->store->getStoreById($this->store_id);
    
    // get appearance settings
    $this->apparance = $this->widget_configuration->getOne($this->current_store_member_id, 'appearance');
    
    if(isset($this->apparance['width']))
      $this->widget_width = $this->apparance['width'];
    else
      $this->widget_width = default_embed_code ('width');
    
    if($this->apparance)
    {
      $this->apparance = json_decode($this->apparance["functions"], true);
    }
    
    if(!is_array($this->apparance))
    {
      $this->apparance = array();
    }
    
    require_once APPPATH . 'config/custom/settings.php';
    require_once APPPATH . '/libraries/qaCssParser.php';
    
    // css parser
    $this->qaCssParser = new qaCssParser($this->store_id);
    $this->widget_css_file = get_store_dir_url($this->store_id).'widget.css';
    
    // sub links slot
    $this->store_slot = array(
      'store'           =>  $this->store_data[0],
      'sub_links'       =>  get_sub_links('settings'),
      'selected'        =>  'settings',
      'inner_links'     =>  get_inner_links_array('settings'),
      'inner_selected'  =>  'Visual'
    );
  }

  /**
   * 
   * function appearance
   * 
   * 
   */
  function appearance() 
  {
    $error = array();
    $file_path = '';

    // default embed code
    $data = default_embed_code();

    // widget configuration
    if ($this->apparance)
    {
      $data = $this->apparance;

      /*if (isset($this->apparance['tabs']))
      {
        $this->apparance['tabs'] = json_decode($this->apparance['tabs'], true);
      }*/

      $data['widget_configuration'] = $this->apparance;
    }  
    
    parse_embed_code($data);

    // if post data
    if ($this->input->post('width'))
    {
      $base_path = get_store_path($this->store_id);

      process_image($data, 'icon_path', $error, $base_path, 'store_logo');

      if ($this->input->post('voting_option') == 'custom')
      {
        process_image($data, 'vote_positive_image', $error_, $base_path, 'pos_vote_image', 16, 16);

        process_image($data, 'vote_negative_image', $error_, $base_path, 'neg_vote_image', 16, 16);
      }
      else
      {
        delete_images($base_path, $data["vote_positive_image"]);
        delete_images($base_path, $data["vote_negative_image"]);
        
        $data['vote_positive_image'] = '';
        $data['vote_negative_image'] = '';
      }

      // save data
      $appearance_row = array (
        'width'               => $this->input->post('width'),
        'height'              => $this->input->post('height'),
        'height_opt'          => $this->input->post('height_opt'),
        'font_family'         => $this->input->post('font_family'),
        'font_color'          => $this->input->post('font_color'),
        'link_color'          => $this->input->post('link_color'),
        'action_text_color'   => $this->input->post('action_text_color'),
        'icon_path'           => $data['icon_path'],
        'voting_option'       => $this->input->post('voting_option'),
        'popular_tab'         => $this->input->post('popular_tab'),
        'recent_tab'          => $this->input->post('recent_tab'),
        'unanswered_tab'      => $this->input->post('unanswered_tab'),
        'vote_positive_image' => $data['vote_positive_image'],
        'vote_negative_image' => $data['vote_negative_image']
      );

      if (empty($error))
      {
        $save_row = array(
          'functions' => $appearance_row
        );

        $this->widget_configuration->updateConfig($this->current_store_member_id, 'appearance', $save_row);
        
        $this->qaCssParser->appearanceCSS($appearance_row);

        $data = $appearance_row;
        $data['widget_configuration'] = $this->apparance;
        parse_embed_code($data);
      }
    }
    $data['error'] = $error;
    /*echo '<pre>';
    print_r($data);
    echo '</pre>';*/
    $this->load->view('settings/appearance', $data);
  }

  /**
   * 
   * function question
   * 
   * 
   */
  function question($store_id = "")
  {
    $type = $this->input->post("type");
    
    if (trim($type))
    {
      $save_row = array(
        'functions' => array($type => $this->input->post("value"))
      );

      $this->widget_configuration->updateConfig($this->current_store_member_id, 'question', $save_row);
      exit;
    }
    
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, 'question');

    $data = $member_settings["functions"];
    $data = json_decode($data, true);
    $this->row = $member_settings;
    
    validate_question_functions($data);
    
    $this->load->view('settings/question', $data);
  }

  /**
   * 
   * function saveAppearanceConfig
   * 
   * 
   */
  function saveAppearanceConfig($store_id, $type = "" , $value = "", $image = "")
  {
    $type = $this->input->post('type');
    $value = $this->input->post('value');

    if (strpos($type, '_tab') !== FALSE)
    {
      $value = array($type => $value);
      $type = 'tabs';      
    }    

    $data[$type] =  $value;
    
    $save_row = array(
      'functions' => $data
    );

    $this->widget_configuration->updateConfig($this->current_store_member_id, 'appearance', $save_row);    
    exit;
  }

  function saveEmail($store_id, $setting,$type = "", $value = "",$image_type = "", $image_value = "", $default = "") {
    
    if($type == "" && $value == "")
    {      
      $type = $this->input->post('type');
      $value = $this->input->post('value');
      $data = array(
        $type => $value
      );      
    }
    else
    {
      $button_data = array(
          $type => $value
      );        


      $data_image = array();

      if (isset($_FILES['upload_image']['tmp_name']) && trim($_FILES['upload_image']['tmp_name'])) 
      {      
        $button_data[$image_type] = $_FILES['upload_image']["name"];
        $base_path = get_store_path($this->store_id);
        process_image($button_data, $image_type, $error_banner, $base_path, 'upload_image', 30, 30);

        //for security reason, we force to remove all uploaded file
        @unlink($_FILES['upload_image']);
      }    
      elseif($default != "")
      {
       $button_data[$image_type] = $default.".jpg"; 
      }    
      else
      {
       $button_data[$image_type] = $image_value.".jpg"; 
      }

      $save_row = array(
            'functions' => $button_data
      );

      $this->widget_configuration->updateConfig($this->current_store_member_id, $setting, $save_row);
    }
    
    if ($setting == "contributor" && strpos($type, 'date') !== FALSE) {
      $save_row = array(
          'functions' => $data
      );
      $this->widget_configuration->updateConfig($this->current_store_member_id, $setting, $save_row);
      exit(1);
    }    
    if(isset($data) && is_array($data))
    {      
      $this->widget_configuration->updateConfig($this->current_store_member_id, $setting, $data);
    }

    exit(1);
  }

  /**
   * 
   * function answer
   * 
   * 
   */
  function answer()
  {
    $type = $this->input->post("type");

    if (trim($type))
    {
      $save_row = array(
        'functions' => array($type => $this->input->post("value"))
      );

      $this->widget_configuration->updateConfig($this->current_store_member_id, 'answer', $save_row);
      exit;
    }
    
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, 'answer');
    
    $data = $member_settings["functions"];
    $data = json_decode($data, true);
    $this->row = $member_settings;
    
    validate_question_functions($data);
    
    $this->load->view('settings/answer', $data);
  }

  /**
   * 
   * function badge_email
   * 
   * 
   */
  function badge_email($store_id, $error = false)
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "badge_email");
    $data["appearance"] = $member_settings["functions"];
    $data["row"] = $member_settings;
    $data["appearance"] = json_decode($data["appearance"], true);
    if ($error)
    {
      $data["error"] = "invalid Image is uploaded";
    }
    
    $this->load->view("settings/badge_email", $data);
  }

  /**
   *
   * function answer_email
   * 
   * 
   */
  function answer_email($store_id, $error = false)
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "answer_email");

    $data["appearance"] = $member_settings["functions"];
    $data["row"] = $member_settings;
    $data["appearance"] = json_decode($data["appearance"], true);
    if ($error)
      $data["error"] = "invalid Image is uploaded";
    $this->load->view("settings/answer_email", $data);
  }

  /**
   *
   * function question_email
   * 
   * 
   */
  function question_email($store_id, $error = false)
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "question_email");

    $data["appearance"] = $member_settings["functions"];
    $data["row"] = $member_settings;
    $data["appearance"] = json_decode($data["appearance"], true);
    if ($error)
      $data["error"] = "invalid Image is uploaded";
    $this->load->view("settings/question_email", $data);
  }

  /**
   *
   * function activity_email
   *
   * 
   */
  function activity_email($store_id, $error = false)
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "activity_email");

    $data["appearance"] = $member_settings["functions"];
    $data["row"] = $member_settings;
    $data["appearance"] = json_decode($data["appearance"], true);
    
    if ($error)
    {
      $data["error"] = "invalid Image is uploaded";
    }
    
    $this->load->view("settings/activity_email", $data);
  }

  /**
   *
   * function contributor_email
   *
   * 
   */
  function contributor()
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "contributor");

    $data["appearance"] = $member_settings["functions"];
    $data["row"] = $member_settings;
    $data["appearance"] = json_decode($data["appearance"], true);

    $this->load->view("settings/contributor", $data);
  }

  /**
   *
   * function thank_you
   *
   * 
   */
  function thank_you($store_id, $error = false) 
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, "thank_you");

    $data["appearance"] = $member_settings["functions"];
    $data["appearance"] = json_decode($data["appearance"], true);
    
    $data["row"] = $member_settings;
    
    if ($error)
    {
      $data["error"] = "invalid Image is uploaded";
    }
    
    $this->load->view("settings/thank_you", $data);
  }
  
  /**
   *
   * function functions_options
   *
   * 
   */
  function functions_options($store_id, $type)
  {
    $member_settings = $this->widget_configuration->getOne($this->current_store_member_id, $type);
    
    $image = $this->input->post("picture", 1);
    $width = $this->input->post("width");
    $height = $this->input->post("height");

    $data = array(
      "image"      => $image,
      "email_name" => $this->input->post("email"),
      "from"       => $this->input->post("from"),
      "image_name" => trim($this->input->post("custom_pic") || $image == "none") ? $this->input->post("custom_pic") : $this->input->post("edit_image")
    );
    
    $base_path = get_store_path($this->store_id);

    if ($image == "default")
    {
      $data["default_text"] = $this->input->post("default_text");
      $data["font_color"] = $this->input->post("font_color");
    }
    elseif ($image == "custom")
    {
      process_image($data, 'image_name', $error_banner, $base_path, 'custom_pic', $this->widget_width, 40, true);
      if (!empty($error_banner))
      {
        redirect("/settings/" . $type . "/" . $this->store_id . "/" . true);
      }
    }
    else 
     {
      delete_images($base_path, $data["image_name"]);
      delete_images($base_path, $data["image_name"]);
    }
    
    $save_row = array(
      'functions' => $data
    );

    $this->widget_configuration->updateConfig($this->current_store_member_id, $type, $save_row);
    
    $this->qaCssParser->bannerImageCSS($data, $this->widget_width, $type);
    
    redirect("/settings/" . $type . "/" . $this->store_id);
  }

  /**
   * 
   * function contributor_functions
   * 
   * 
   */
  function contributor_functions($store_id, $type)
  {
    $service_data = $this->widget_configuration->getOne($this->current_store_member_id, $type);
    if($service_data)
    {
      $service_data = json_decode($service_data["functions"], true);
    }
    
    $avtar_option = $this->input->post("avatar_option", 1);
    $header_option = $this->input->post("header_option", 1);
    
    $base_path = get_store_path($this->store_id);
    
    $service_data['avatar']['option'] = $avtar_option;
    $service_data['header']['option'] = $header_option;

    if ($avtar_option == "default")
    {
      //$service_data["avatar"]['text'] = $this->input->post("avatar_text");
      $service_data["avatar"]['color'] = $this->input->post("avatar_color");
    }
    elseif ($avtar_option == "custom")
    {
      process_image($service_data['avatar'], 'image', $error_, $base_path, 'avatar_image', 32, 32, true);
    }
    
    if ($header_option == "default")
    {
      $service_data["header"]['text'] = $this->input->post("header_text");
      $service_data["header"]['color'] = $this->input->post("header_color");
    }
    elseif ($header_option == "custom")
    {
      process_image($service_data['header'], 'image', $error_header, $base_path, 'header_image', $this->widget_width, 40, true);
    }

    $save_row = array(
      'functions' => $service_data
    );

    $this->widget_configuration->updateConfig($this->current_store_member_id, $type, $save_row);
    
    $this->qaCssParser->contributorCSS($service_data, $this->widget_width);
    
    redirect("settings/" . $type . "/" . $this->store_id);
  }
  
  
  
  /**
   * 
   * function saveButtonConfig
   * 
   * @param <int>       $store_id
   * @param <string>    $service
   * @param <string>    $button_index
   * @param <string>    $button_text
   * @param <string>    $button_class
   * @param <string>    $button_type
   *  
   */
  function saveButtonConfig()
  {
    $service = $this->input->post('service');
    $button_index = $this->input->post('button_index');
    $button_text = $this->input->post('button_text');
    $button_class = $this->input->post('button_class');
    $button_color = $this->input->post('button_color');
    $button_type = $this->input->post('button_type');
    $button_height = $this->input->post("button_height");
    $button_width = $this->input->post("button_width");
    
    $image_url = '';
    
    $this->service_data = $this->widget_configuration->getOne($this->current_store_member_id, $service);
    if($this->service_data)
    {
      $this->service_data = json_decode($this->service_data["functions"], true);
    }
        
    $data = array(
      'text'   => $button_text,
      'class'  => $button_class,
      'color'  => $button_color,
      'type'   => $button_type,
      'height' => $button_height,
      'width'  => $button_width
    );
    
    // old image info
    $image_info = array();
    
    if(isset($this->service_data[$button_index]['image']))
    {
      $image_info['image'] = $this->service_data[$button_index]['image'];
    }
    
    // upload new image
    if($button_type == 'custom' && isset($_FILES['upload_image']['tmp_name']) && trim($_FILES['upload_image']['tmp_name']))
    {
      $base_path = get_store_path($this->store_id);
      if(trim($this->input->post("custom_button")))
      {
        process_image($image_info, 'image', $error_banner, $base_path, 'upload_image', $button_width, $button_height,true,false);
      }
      else
      {
        list($w, $h) = getimagesize($image_path);
        process_image($image_info, 'image', $error_banner, $base_path, 'upload_image',$w,$h,true,false);
      }

      //for security reason, we force to remove all uploaded file
      @unlink($_FILES['upload_image']);
      
      $data['class'] = 'qaw-btn-custom';
    }
    elseif(trim($button_class))
    {
      unset($image_info['image']);
    }
    
    if(isset($image_info['image']))
    {
      $data['image'] = $image_info['image'];
    }
    
    if(strpos($data['class'], 'qaw-btn-custom') === FALSE)
    {
      $data['class'] .= ' qaw-btn-custom';
    }
    
    $this->service_data[$button_index] = $data;
    
    // save data
    $save_row = array(
      'functions' => $this->service_data
    );

    // save button
    $this->widget_configuration->updateConfig($this->current_store_member_id, $service, $save_row);
    
    // save css
    $image_url = $this->qaCssParser->customButtonCSS($data, '#'.$this->custom_config['buttons'][$button_index].'.qaw-btn-custom', $button_width, $button_height);
    
    exit(render_json_response(array($image_url)));
  }
  
  /**
   * 
   * function getTabsHtml
   * 
   * 
   */
  function getTabsHtml($store_id, $type = "")
  {
    $this->no_layout = true;

    $functions = $this->input->post("functions");
    $functions = json_decode($functions, true);
    
    $data["type"] = $type;
    $data = array_merge($data, $this->apparance);
    
    parse_embed_code($data);
    
    $data['widget_configuration'] = $this->apparance;
    
    $this->load->view("settings/tabs_html", $data);
  }
}

