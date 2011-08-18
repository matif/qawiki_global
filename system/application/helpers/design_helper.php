<?php

function list_records_table($rows, $fields, $table_class = 'stores-list')
{
  $html = '<table cellpadding="0" cellspacing="0" width="100%" class="'.$table_class.'" border="1px">
    <thead>
      <tr>';

  foreach ($fields as $field)
  {
    if(isset($field['skip']) && $field['skip'])
      continue;

    $html .= '<th><strong>'.(isset($field[0]['heading']) ? $field[0]['heading'] : $field['heading']).'</strong></th>';
  }

  $html .= '</tr>
    </thead>
    <tbody>';
  
  if(count($rows) > 0)
  {
    foreach ($rows as $row)
    {
      if($row instanceof stdClass)
      {
        $row = (array) $row;
      }

      $html .= '<tr>';
      foreach ($fields as $field)
      {
        if(isset($field['skip']) && $field['skip'])
          continue;

        $align = (isset($field['src']) || isset($field['type']) || isset($field['link']) || isset($field['callback']) || isset($field['rel'])) ? 'align="center"' : '';

        $html .= '<td '.$align.'>';

        if(isset($field[1]))
        {
          foreach ($field as $val)
          {
            if(isset($val['skip']) && $val['skip'])
              continue;

              $html .= make_cell_data($row, $val);
          }
        }
        else
        {
          $html .= make_cell_data($row, $field);
        }

        $html .= '</td>';
      }
      $html .= '</tr>';
    }
  }
  else
  {
    $html .= '<tr>
        <td colspan="'.count($fields).'" align="center">No Record Found!!</td>
      </tr>';
  }

  $html .= '</table>';
  
  return $html;
}

/**
 *
 * make cell data
 *
 */
function make_cell_data($row, $field)
{
  $html = '';

  compare_data($row, $field);

  if(isset($field['link']) || isset($field['callback']) || isset($field['rel']))
  {
    $html .= get_link_tag($row, $field);
  }
  elseif(isset($field['type']))
  {
    $html .= get_form_element($row, $field);
  }
  elseif(isset($field['src']))
  {
    $html .= get_image_tag($row, $field);
  }
  elseif(isset($field['url']) && trim($row[$field['url']]))
  {
    $html .= get_link_tag($row, $field);
  }
  else
  {
    $html .= isset($row[$field['text']]) ? $row[$field['text']] : $field['text'];
  }

  return $html;
}

/**
 *
 * Parse and replace dynamic vars
 *
 */
function parse_dynamic_vars($str, $row)
{
  preg_match_all('/\{.*?\}/si', $str, $tokens);

  if($tokens)
  {
    foreach($tokens[0] as $token)
    {
      $str = str_replace($token, $row[str_replace(array('{', '}'), array('', ''), $token)], $str);
    }
  }

  return $str;
}

/**
 *
 * Make link element
 *
 */
function get_link_tag($row, $field)
{
  $html = '<a '.(isset($field['class']) ? 'class="'.$field['class'].'"' : '').' '.(isset($field['target']) ? 'target="'.$field['target'].'"' : '');

  if(isset($field['url']))
  {
    $field['link'] = $row[$field['url']];
  }

  if(isset($field['link']))
  {
    $html .= ' href="'.parse_dynamic_vars($field['link'], $row).'"';
  }
  else
  {
    $html .= ' href="javascript:;"';
  }

  if(isset($field['callback']))
  {
    $html .= ' onclick="'.parse_dynamic_vars($field['callback'], $row).'"';
  }

  if(isset($field['rel']))
  {
    $html .= ' rel="'.parse_dynamic_vars($field['rel'], $row).'"';
  }

  $html .= '>'.(isset($row[$field['text']]) ? $row[$field['text']] : $field['text']).'</a>';

  return $html;
}

/**
 *
 * Make input elements
 *
 */
function get_form_element($row, $field)
{
  if($field['type'] == 'select')
  {
    $html = get_select_tag($row, $field);
  }
  else
  {
    $html = '<input type="'.$field['type'].'" name="'.$field['name'].'" value="'.parse_dynamic_vars($field['value'], $row).'" '.(isset($field['class']) ? 'class="'.$field['class'].'"' : '').' />';
  }

  return $html;
}

/**
 *
 * Make image tag
 *
 */
function get_image_tag($row, $field)
{
  if(trim($row[$field['src']]))
  {
    $html = '<img height="'.$field['height'].'" height="'.$field['width'].'" src="'.parse_dynamic_vars($row[$field['src']], $row).'" />';
  }
  else
  {
    $html = $field['default'];
  }

  return $html;
}

/**
 *
 * Make select tag
 *
 */
function get_select_tag($row, $field)
{
  if(isset($field['value']))
  {
    $field['value'] = parse_dynamic_vars($field['value'], $row);
  }

  $html = '<select name="'.$field['name'].'">';
  foreach($field['options'] as $key => $value)
  {
    $html .= '<option value="'.$key.'" '.(isset($field['value']) && $field['value'] == $key ? 'selected="selected"' : '').'>'.$value.'</option>';
  }

  $html .= '</select>';

  return $html;
}

/**
 *
 * Compare values
 *
 */
function compare_data($row, &$field)
{
  if(isset($field['compare']))
  {
    $text = parse_dynamic_vars($field['compare'], $row);

    if($text == $field['compare_with'])
    {
      $field = array(
        'text'  =>  '-'
      );
    }
  }

}

function get_html_element($tag, $config)
{
  $html = '<'.$tag;

  foreach($config as $key => $value)
  {
    if($key != $tag && !is_array($value))
      $html .= ' '.$key.'="'.$value.'"';
  }

  $html .= '>'.$config[$tag].'</'.$tag.'>';

  return $html;
}

function search_component($fields, $action = '', $method = 'post', $class = 'constrain')
{
  $html = '<form action="'.$action.'" id="productFrm" class="'.$class.'" method="'.$method.'">';

  foreach($fields as $field)
  {
    $html .= '<div>';
    $html .= isset($field['label']) ? get_html_element('label', $field) : '';
    if(isset($field['input']))
    {
      $html .= get_form_element(array(), $field['input']);
    }
    elseif(isset($field['type']))
    {
      $html .= get_form_element(array(), $field);
    }
    $html .= '</div>';
  }

  $html .= '</form>';

  return $html;
}

/*------------------------------------------------------------------------ */
/*----------------------------- NEW UI ----------------------------------- */
/*------------------------------------------------------------------------ */

function grid_title_html($title, $accordion_state = 'open')
{
  return '
    <div class="heading_section  clearfix grid-header">
      <div class="head_rpt mc">'.$title.'</div>
      <div class="accordian_'.$accordion_state.'"><a href="javascript:;"></a></div>
    </div>';
}

function moderate_select_tag()
{
  $data = array(
    'valid'      => 'Valid',
    'invalid'    => 'Invalid',
    'irrelevant' => 'Irrelevant',
    'abusive'    => 'Abusive'
  );
  
  return select_tag('moderate', $data);
}

function editable_content($content, $rel, $block =  false, $display = true)
{
  $html = '<span class="editable '.$rel.'" '.($display ? '' : 'style="display:none"').'>
      <span class="edit-data">'.$content.'</span>
      <a href="javascript:;" class="editable-link" rel="'.$rel.'">Edit</a>
    </span>';
  
  if($block)
  {
    $html = '<div>'.$html.'</div>';
  }
  
  return $html;
}

function edit_dialog()
{
  return '<div id="inline-edit-dlg" style="display: none">
  <div class="dlg-content">
    <div class="dlg-row">
      <label>Edit: </label>
      <textarea name="edit_text" id="edit_text"></textarea>
    </div>
    <div class="dlg-row">
      <label>&nbsp;</label>
      <input type="button" name="edit_save" id="edit_save" value="" class="btn_save" />
    </div>
  </div>
</div>';
}

function edit_button_dialog($service = 'appearance')
{
  return '    
    <div id="inline-edit-btn-dlg" style="display: none">
    <form action = "" method = "" id = "" enctype="multipart/form-data"/>
    <div class ="error" id = "btn_err"></div>
    <div class="dlg-content">
      <div class="row_dat">
        <div class="lbel">Edit:</div>
        <div class="lbl_inpuCnt">
          <textarea name="edit_button" id="edit_button" class="textarea" style="width:255px"></textarea>
        </div>
        <div class="clear"></div>
      </div>      
      <div class="row_dat">
        <div class="lbel">Font Color:</div>
        <div class="lbl_inpuCnt">
          <input type="text" class="input-fld" value="000000" id="font_image" name="font_image" />
        </div>
        <div class="clear"></div>
      </div>
      <div class="row_dat">
        <div class="lbel">Button Style:</div>
        <div class="lbl_inpuCnt" style="width:auto">
          <label class="radio-cont" style="display:block">
            <input type="radio" value="default" name="button_type" id="button_style_default" checked="checked" onchange="$(\'#button-styles\').show();$(\'#button-custom-img\').hide();" />
            <span class="avatat_tag">Default</span>
          </label>
          <div class="clear"></div>
          <div id="button-styles" class="lbl_inpuCnt">
            <a href="javascript:;" onclick="attach_select_image(this)" rel ="qaw-buton-pink" class="qaw-buton-pink qaw-button"><span>i am here</span></a>
            <a href="javascript:;" onclick="attach_select_image(this)" rel ="qaw-buton-gray" class="qaw-buton-gray qaw-button"><span>i am here</span></a>
            <a href="javascript:;" onclick="attach_select_image(this)" rel ="qaw-buton-yellow" class="qaw-buton-yellow qaw-button"><span>i am here</span></a>
          </div>
          <div class="clear"></div>
          <label class="radio-cont" style="display:block">
            <input type="radio" value="custom" name="button_type" id="button_style_custom" onchange="$(\'#button-styles\').hide();$(\'#button-custom-img\').show();" />
            <span class="avatat_tag">Custom Image</span>
          </label>
          <div class="clear"></div>
          <div id="button-custom-img" class="lbl_inpuCnt" style="display:none">
            <div class="lbl_inpuCnt" style = "padding-bottom:10px;">
              <input type = "file" name="upload_image" id="image_upload"/>
            </div>
            <div class="clear"></div>
            <div style= "padding-left:10px;">
              <label class="radio-cont" style="display:block">
                <input type="radio" value="default_size" name="button_settings" checked="checked" id="button_default_size" onchange="$(\'#custom-height\').hide();" \/>
                <span class="avatat_tag">Default,Width and Height</span>
              </label>
              <div class="clear"></div>
              <label class="radio-cont" style="display:block">            
                <input type="radio" value="custom_size" name="button_settings" id="button_custom_size" onchange="$(\'#custom-height\').show();" \/>
                <span class="avatat_tag">Custom </span>
              </label>
            </div>
            <div class="clear"></div>
            <div style= "padding-left:30px;">
              <div style = "display:none" id = "custom-height">              
                <div style = "padding-top:10px;">
                  <label style = "min-width:60px;">Width</label>
                  <input type = "text" name="width" id="image_width"/>
                </div>
                <div class="clear"></div>

                <div style = "padding-top:10px;">
                  <label style = "min-width:60px;">Hieght</label>
                  <input type = "text" name="height" id="image_height"/>
                </div>              
              </div>
             </div>
          </div>
        </div>
        <input type="hidden" id = "image_url"  name="image_url" value="" />
        <input type="hidden" id = "edit_button_service"  name="edit_button_service" value="'.$service.'" />
        <div class="clear"></div>
      </div>
      
      <div class="row_dat">
        <div class="lbel">&nbsp;</div>
        <input type="button" name="edit_save" id="button_save" value="" class="btn_save" />
      </div>
  </form>
  <div id = "preview_image"></div>
</div>';
}

/**
 * 
 * function button_image
 * 
 * @param <string>     $button_index
 * @param <string>     $default_button
 * @param <string>     $default_text
 * @param <array>     $array
 * 
 */
function widget_button($button_index, $default_button, $default_text, $button_id = 'qaw-ask-question', $array = array(), $show_edit = true)
{
  $var = null;
  
  if(!empty($array) && isset($array[$button_index]))
  {
    $var = $array[$button_index];
  }
  elseif(isset($$button_index))
  {
    $var = $$button_index;
  }
  
  $button_class = ($var && trim($var['class']) ? $var['class'] : $default_button);
  $button_image = ($var && isset($var['image']) && trim($var['image']) ? $var['image'] : '');
  $button_type = ($var && isset($var['type']) && trim($var['type']) ? $var['type'] : 'default');
  
  $html  = '<a id="'.$button_id.'" class="'.$button_class.' qaw-button" href="javascript:;">';
  $html .=   '<span>'.($var && trim($var['text']) ? $var['text'] : $default_text).'</span>';
  $html .= '</a>';
  
  if($show_edit)
  {
    $html .= '&nbsp;<a href="javascript:;"><img src="'.base_url().'images/frontend/ico_edit.png" alt="Edit" title="Edit" width="16" height="16" align="absmiddle" class="editable-button"  rel="'.$button_index.'|'.$button_class.'|'.$button_image.'|'.$button_type.'" /></a>';
  }
  
  return $html;
}

/**
 * 
 * function widget_popup_banner
 * 
 * @param <array>   $data
 * 
 */
function widget_popup_banner($data, $class_name)
{  
  $text = '';
  $color = '' ;
  if(isset($data['option']) && $data['option'] == 'default')
  {
    $text = $data['text'];
    $color = $data['color'];
  }
  elseif(isset($data['default_text']) && $data['image'] == 'default')
  {
    $text = $data['default_text'];
    $color = $data['font_color'];
  }
  
  return '<div class="'.$class_name.'" style="max-width:640px;font-size:18px;color:#'.$color.'">'.$text.'</div>
    <div class="clear"></div>';
}