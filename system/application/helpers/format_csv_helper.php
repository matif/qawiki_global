<?php

function csv_detect_columns(&$csv)
{
  $CI =& get_instance();
  
  $columns = array();
  $distinct = array();
  
  if (isset($csv[0]))
  {
    $config_csv_fields = $CI->config->item('csv_fields');
    
    $csv[0] = array_map('strtolower', $csv[0]);
    $csv[0] = array_map('trim', $csv[0]);

    foreach($csv[0] as $key => $value)
    {
      if(isset($distinct[$value]))
        continue;
      
      $distinct[$value] = true;
      
      if (isset($config_csv_fields[$value]))
      {
        $columns[$key] = array(
          'id'   => $value,
          'name' => $config_csv_fields[$value]
        );
      }
    }
  }
  
  return $columns;
}

function csv_columns_count($csv)
{
  return (isset($csv[0])) ? count($csv[0]) : 0;
}

function csv_saved_columns($columns)
{  
  $html = '';
    
  foreach($columns as $key => $value)
  {
    $html .= '<input type="hidden" name="hid_'.$key.'" id="hid_'.$key.'" value="'.$value['id'].'" />';
  }
  
  return $html;
}

function csv_format_header($csv, $columns)
{
  $CI =& get_instance();
  
  $html = '';
  $first = true;
  
  if (isset($csv[0]))
  {
    $disabled = array();
    foreach($columns as $column)
    {
      $disabled[] = $column['id'];
    }
    
    foreach($csv[0] as $key => $value)
    {
      // if among detected columns
      if (isset($columns[$key]))
      {
        $html .= '<th id="th_'.$key.'" class="saved" style ="padding-left:7px">'.$columns[$key]['name'].'<br>'.$columns[$key]['id'].'<br>
            <a href="javascript:;" onclick="showEditFieldSingle('.$key.');">Edit</a>&nbsp;|&nbsp;
            <a href="javascript:;" onclick="deleteColField('.$key.');">Delete</a>
          </th>';
      }
      elseif($first)
      {
        $first = false;
        
        $html .= '<th id="th_'.$key.'" class="editSelect" style ="padding-left:7px">
            '.select_tag($key, $CI->config->item('csv_fields'), '', array('onchange' => 'changeBg('.$key.')'), $disabled).'
            <br />
            <a href="javascript:;" onclick="saveColName('.$key.');">Save</a>&nbsp;|&nbsp;
            <a href="javascript:;" onclick="deleteColField('.$key.');">Delete</a>
          </th>';
      }
      else
      {
        $html .= '<th id="th_'.$key.'" style ="padding-left:7px">
            <p>
              unnamed column<br />
              <a href="javascript:;" onclick="showEditField('.$key.');">Edit</a>&nbsp;|&nbsp;
              <a href="javascript:;" onclick="deleteColField('.$key.');">Delete</a>
            </p>
          </th>';
      }
    }
  }
  
  return $html;
}