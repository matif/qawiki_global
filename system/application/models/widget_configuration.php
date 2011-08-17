<?php



class Widget_configuration extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  function getOne($member_id, $type, $format = false)
  {
    $this->db->where('team_member_id', $member_id);
    $this->db->where('type', $type);
    
    $this->db->limit(1);
    
    $row = $this->db->get('widget_configuration')->result_array();
    
    $row = ($row) ? $row[0] : null;
    
    if($format)
    {
      $row = $this->formatDefaults($row, $type);
    }
    
    return $row;
  }
  
  function save($data)
  {
    $this->db->insert('widget_configuration', $data);
  }
  
  function update($id, $data)
  {
    $this->db->where('id', $id);
      
    $this->db->update('widget_configuration', $data);
  }
  
  function updateConfig($member_id, $type, $data)
  {
    $row = $this->getOne($member_id, $type);
    
    $this->formatTabs($data, $row);
    
    if(!$row)
    {
      $data['team_member_id'] = $member_id;
      $data['type'] = $type;
      $data['created_at'] = gmdate('Y-m-d H:i:s');     
     
      $this->save($data);
    }
    else
    {      
      $this->update($row['id'], $data);
    }
  }
  
  function formatTabs(&$data, $row)
  {
    if(isset($data['tabs']))
    {
      if($row)
      {
        $tabs = json_decode($row['tabs'], true);
        foreach($tabs as $key => $tab)
        {
          if(!isset($data['tabs'][$key]))
          {
            $data['tabs'][$key] = $tab;
          }
        }
      }
      
      $data['tabs'] = json_encode($data['tabs']);
    }
    
    if(isset($data['functions']) && $row)
    {
      $functions = json_decode($row['functions'], true);
      
      foreach($functions as $key => $function)
      {
        if(!isset($data['functions'][$key]))
        {
          $data['functions'][$key] = $function;
        }
      }
      
      if(isset($functions['tabs']))
      {
        foreach($functions['tabs'] as $key => $tab)
        {
          if(!isset($data['functions']['tabs'][$key]))
          {
            $data['functions']['tabs'][$key] = $tab;
          }
        }
      }
      
      $data['functions'] = json_encode($data['functions']);
    }
  }
  
  /**
   * 
   * function formatDefaults
   * 
   * @param <array>     $row
   * @param <string>    $type
   * 
   */
  function formatDefaults($row, $type)
  {
    $CI =& get_instance();
    
    if(isset($row['functions']))
    {
      $row['functions'] = json_decode($row['functions'], true);
    }

    if(isset($CI->custom_config[$type.'_default']))
    {
      $functionName = 'format'.ucfirst($type);
      
      $defaults = $CI->custom_config;
      
      if(method_exists($this, $functionName))
      {
        $row = $this->$functionName($row, $defaults, $type.'_default');
      }
    }
    
    return $row;
  }
  
  /**
   * 
   * function formatAppearance
   * 
   * @param <array>     $row
   * @param <array>     $defaults
   * @param <string>    $type_index
   * 
   */
  function formatAppearance($row, $defaults, $type_index)
  {
    if(!isset($row['functions']['ask_question']))
    {
      $row['functions']['ask_question'] = array(
        'text'     =>   $defaults[$type_index]['question_button'],
        'color'    =>   $defaults['button_color'],
        'class'    =>   $defaults['default_button']
      );
    }
    
    if(!isset($row['functions']['answer_it']))
    {
      $row['functions']['answer_it'] = array(
        'text'     =>   $defaults[$type_index]['answer_button'],
        'color'    =>   $defaults['button_color'],
        'class'    =>   $defaults['default_button']
      );
    }
    
    if(!isset($row['functions']['search_button']))
    {
      $row['functions']['search_button'] = array(
        'text'     =>   $defaults[$type_index]['search_button'],
        'color'    =>   $defaults['button_color'],
        'class'    =>   $defaults['default_button']
      );
    }

    foreach($defaults[$type_index]['tabs'] as $key => $tab)
    {
      if(!isset($row['functions']['tabs'][$key]))
      {
        $row['functions']['tabs'][$key] = $tab;
      }
    }
    
    if(!isset($row['title']) || !trim($row['title']))
    {
      $row['title'] = $defaults[$type_index]['title'];
    }
    
    return $row;
  }
  
  /**
   * 
   * function formatContributor
   * 
   * @param <array>     $row
   * @param <array>     $defaults
   * @param <string>    $type_index
   * 
   */
  function formatContributor($row, $defaults, $type_index)
  {
    if(!isset($row['functions']['contributor']))
    {
      $row['functions']['contributor'] = array(
        'text'     =>   $defaults[$type_index]['contributor_btn'],
        'color'    =>   $defaults['button_color'],
        'class'    =>   $defaults['default_button']
      );
    }
    
    if(!isset($row['functions']['header']['text']) || $row['functions']['header']['option'] != 'default')
    {
      $row['functions']['head']['text'] = '';
    }
    
    if(!isset($row['title']) || !trim($row['title']))
    {
      $row['title'] = $defaults[$type_index]['title'];
    }
    
    return $row;
  }

  /**
   * 
   * function formatQuestion
   * 
   * @param <array>     $row
   * @param <array>     $defaults
   * @param <string>    $type_index
   * 
   */
  function formatQuestion($row, $defaults, $type_index)
  {
    if(!isset($row['title']) || !trim($row['title']))
    {
      $row['title'] = $defaults[$type_index]['title'];
    }
    
    if(!isset($row['sub_title']) || !trim($row['sub_title']))
    {
      $row['sub_title'] = $defaults[$type_index]['sub_title'];
    }
    
    foreach(array('products', 'categories', 'brands') as $option)
    {
      if(!isset($row['functions'][$option]))
      {
        $row['functions'][$option] = 'on';
      }
    }
    
    return $row;
  }
  
  /**
   * 
   * function formatAnswer
   * 
   * @param <array>     $row
   * @param <array>     $defaults
   * @param <string>    $type_index
   * 
   */
  function formatAnswer($row, $defaults, $type_index)
  {
    $row = $this->formatQuestion($row, $defaults, $type_index);
    
    return $row;
  }
  
  /**
   * 
   * function getWidgetSettings
   * 
   * @param <int>  $team_member_id
   * 
   */
  function getWidgetSettings($team_member_id)
  {
    $data['appearance'] = $this->widget_configuration->getOne($team_member_id, 'appearance', true);
    $data['question_dlg'] = $this->widget_configuration->getOne($team_member_id, 'question', true);
    $data['answer_dlg'] = $this->widget_configuration->getOne($team_member_id, 'answer', true);
    
    return $data;
  }
}
