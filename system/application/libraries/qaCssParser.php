<?php


/**
 * 
 * @package - qaCssParser
 * @author  - Kashif
 * 
 * 
 */

require_once dirname(__FILE__).'/cssparser.php';

class qaCssParser extends cssparser
{
  private $base_path;
  private $file_path;
  private $mapping;
  private $store_url;
  
  /**
   * 
   * initialize
   * 
   */
  function __construct($store_id)
  {
    $this->base_path = get_store_path($store_id);
    
    $this->file_path = $this->base_path.'widget.css';
    
    $this->store_url = get_store_dir_url($store_id);
    
    // parse css file
    parent::Parse($this->file_path);
    
    $this->initializeMapping();
  }
  
  /**
   * 
   * function initializeMapping
   * 
   * 
   */
  function initializeMapping()
  {
    $this->mapping = array(
      'text'      =>  '.qawiki-text',
      'link'      =>  'a.qawiki-link, a.qawiki-link:hover, a.qawiki-link:visited',
      'action'    =>  'a.qawiki-action, a.qawiki-action:hover, a.qawiki-action:visited'
    );
  }
  
  /**
   * 
   * function get
   * 
   * @param <string>    $key
   * @param <string>    $property
   * 
   */
  function get($key, $property)
  {
    return parent::Get($key, $property);
  }
  
  /**
   * 
   * function set
   * 
   * @param <string>    $key
   * @param <array>     $properties
   * 
   */
  function set($key, $properties)
  {
    if(isset($this->mapping[$key]))
    {
      $key = $this->mapping[$key];
    }
    
    foreach($properties as $property)
    {
      parent::Add($key, $property);
    }
  }
  
  /**
   * 
   * function save
   * 
   * 
   * 
   */
  function save()
  {
    mk_dir($this->base_path);    
    $css = parent::GetCSS();       
    
//    if(file_exists($this->file_path))
//    {      
//      umask(000);
//      chmod($this->file_path, 0777);
//    }
//    chmod($this->file_path, 0777);
    file_put_contents($this->file_path, $css);    
    
  }
  
  /**
   * 
   * function saveDefaultCSS
   * 
   * @param <string> $css
   * 
   */
  
  function saveDefaultCSS($css)
  {
    mk_dir($this->base_path);

    file_put_contents($this->file_path, $css);
  }
  
  /**
   * 
   * function appearanceCSS
   * 
   * 
   */
  function appearanceCSS($data)
  {
    $global = array(
      'font-family: '.$data['font_family'],
      'font-size:12px',
      'color: #'.$data['font_color'],
      'width: '.$data['width'].'px'
    );
    
    if(isset($data['height_opt']) && $data['height_opt'] == 'custom')
    {
      $global[] = 'height: '.$data['height'].'px';
      $global[] = 'overflow-y: auto';
    }
    else
    {
      $this->unsetAttr('#qaw-widget', array('height', 'overflow-y'));
    }
    
    $this->set('#qaw-widget', $global);
    
    $this->set('#qaw-widget input, #qaw-widget form, #qaw-widget table, #qaw-widget div, #qaw-widget p, #qaw-widget textarea, #qaw-widget a', array(
      'font-family: '.$data['font_family'],
      'font-size:12px'
    ));
    
    $this->set('#qaw-widget a, #qaw-widget a:visited, #qaw-widget a:hover', array(
      'color: #'.$data['link_color']
    ));
    
    $this->set('#qaw-widget a.qaw-action, #qaw-widget a.qaw-action:visited, #qaw-widget a.qaw-action:hover', array(
      'color: #'.$data['action_text_color']
    ));
    
    $this->set('#qaw-widget .qaw-vote', array(
      'height: 16px',
      'display: inline-block',
      'cursor: pointer',
      'padding: 0 3px 0 18px'
    ));
    
    if(isset($data['vote_positive_image']))
    {
      $this->set('#qaw-widget .qaw-vote-up', array(
        'background: url('.$this->store_url.'t-'.$data['vote_positive_image'].') no-repeat center left'
      ));
    }
    
    if(isset($data['vote_negative_image']))
    {
      $this->set('#qaw-widget .qaw-vote-down', array(
        'background: url('.$this->store_url.'t-'.$data['vote_negative_image'].') no-repeat center left'
      ));
    }
    
    $this->save();
  }
  
  /**
   * 
   * function customButtonCSS
   * 
   * 
   */
  function customButtonCSS($data, $name, $w, $h)
  {
    $image_url = '';
  
    if(isset($data['image']) && trim($data['image']))
    {
      $image_url = $this->store_url.'t-'.$data['image'];
      
      if(!$w || !$h || !is_numeric($w) || !is_numeric($h))
      {
        list($w, $h) = @getimagesize($image_url);
    
        if(mb_strlen($data['text']) * 10 > $w)
          $w = mb_strlen($data['text']) * 10;
      }
    
      $css = array(
        'background: url('.$image_url.')',
        'width: '.$w.'px',
        'height: '.$h.'px',
        'color: #'.$data['color'],
        'text-align: center'
      );
    }
    else
    {
      $css = array(
        'color: #'.$data['color']
      );
      
      $this->unsetKey($name);
    }
    
    $this->set($name, $css);
    
    $this->save();
    
    return $image_url;
  }
  
  /**
   * 
   * function customButtonCSS
   * 
   * @param <array>   $data
   * @param <int>     $widget_width
   * 
   */
  function contributorCSS($data, $widget_width)
  {
    // avatar css
    $css = array(
      'width: 32px',
      'height: 32px',
      'float: left',
      'border: 1px solid #E2AFAF',
      'margin: 0 10px 0 0'
    );
    
    if($data['avatar']['option'] == 'default')
    {
      $color = (isset($data['avatar']['color'])) ? $data['avatar']['color'] : 'FF0000';
      
      $css[] = 'background: #'.$color;
    }
    elseif($data['avatar']['option'] == 'custom')
    {
      $image_url = $this->store_url.'t-'.$data['avatar']['image'];
      
      $css[] = 'background: url('.$image_url.') no-repeat';
    }
    
    if($data['avatar']['option'] == 'none')
    {
      $this->unsetKey('.qaw-user-avatar');
    }
    else
    {
      $this->set('.qaw-user-avatar', $css);
    }
    
    // header css
    $css = array(
      'width: '.$widget_width.'px',
      'margin: 0 0 10px 0'
    );
    
    if($data['header']['option'] == 'default')
    {
      $color = (isset($data['header']['color'])) ? $data['header']['color'] : '000000';
      
      $css[] = 'color: #'.$color;
      
      $this->unsetAttr('.qaw-contributor-header', array('height', 'background'));
    }
    elseif($data['header']['option'] == 'custom')
    {
      $image_url = $this->store_url.'t-'.$data['header']['image'];
      
      $css[] = 'background: url('.$image_url.')';
      $css[] = 'height: 40px';
    }
    
    if($data['header']['option'] == 'none')
    {
      $this->unsetKey('.qaw-contributor-header');
    }
    else
    {
      $this->set('.qaw-contributor-header', $css);
    }
    
    $this->save();
  }
  
  /**
   * 
   * function bannerImageCSS
   * 
   * @param <array>   $data
   * @param <int>     $widget_width
   * @param <string>  $service
   * 
   */
  function bannerImageCSS($data, $widget_width, $service)
  {
    $service = str_replace('_', '-', $service);
    
    // header css
    $css = array(
      'width: '.$widget_width.'px',
      'margin: 0 0 10px 0'
    );
    
    if($data['image'] == 'default')
    {
      $color = (isset($data['font_color'])) ? $data['font_color'] : '000000';
      
      $css[] = 'color: #'.$color;
      
      $this->unsetAttr('.qaw-'.$service.'-header', array('height', 'background'));
    }
    elseif($data['image'] == 'custom')
    {
      $image_url = $this->store_url.'t-'.$data['image_name'];
      
      $css[] = 'background: url('.$image_url.')';
      $css[] = 'height: 40px';
    }
    
    if($data['image'] == 'none')
    {
      $this->unsetKey('.qaw-'.$service.'-header');
    }
    else
    {
      $this->set('.qaw-'.$service.'-header', $css);
    }
    
    $this->save();
  } 
  
}
