<?php

/**
 * 
 * jqGrid helper
 * 
 * @author - Kashif
 * 
 */

function render_grid($container_id, $data_route, $config, $data_type = 'json')
{
  $grid = '<script type="text/javascript">
    jQuery("#' . $container_id . '").jqGrid({
      url: "' . $data_route . '",
      datatype: "' . $data_type . '",
      colNames:[' . grid_head($config['head']) . '],
      colModel:[' . grid_columns($config['columns']) . '],
      rowNum:' . $config['record_per_page'] . ',
      rowList:[1, 2, 5, 10, 20, 30],
      pager: "' . $config['pager'] . '", 
      sortname: "' . $config['sort_column'] . '",
      viewrecords: true, 
      sortorder: "' . $config['sort_order'] . '", 
      caption:"' . $config['caption'] . '",
      height: "auto",
      onSelectRow: function(id){
        '.(isset($config['select_row_callback']) ? $config['select_row_callback'].'(id);' : '').'
      }
    });
  ';
  
  /*$('#tblData').setGridParam({
    url:url, 
    datatype: primeSettings.ajaxDataType,  
  });
  $('#tblData').trigger('reloadGrid'); */
  //jQuery("#list2").jqGrid('navGrid','#pager2',{edit:true,add:false,del:false});
  $grid .= '</script>';
  
  return $grid;
}

function grid_head($head)
{ 
  foreach($head as &$value)
  {
    $value = '"'.$value.'"';
  }
  
  return join(',', $head);
}

function grid_columns($columns)
{
  $c = '';
  
  foreach($columns as $index => $column)
  {
    $c .= '{';
    $j = 0;
    $cnt = count($column);
    
    foreach($column as $key => $value)
    {
      if(is_string($value))
      {
        $value = '"' . $value . '"';
      }
      
      if($value === false)
      {
        $c .= $key.':false';
      }
      else
      {
        $c .= $key.':' . $value;
      }
      
      if(++$j < $cnt)
      {
        $c .= ',';
      }
    }
    
    $c .= '}';
    
    if($index < count($columns) - 1)
      $c .= ',';
  }
  
  return $c;
}

function grid_libraries()
{
  use_javascript('jqgrid/js/i18n/grid.locale-en.js');
  use_javascript('jqgrid/plugins/ui.multiselect.js');
  use_javascript('jqgrid/js/jquery.jqGrid.min.js');
  use_javascript('jqgrid/plugins/jquery.tablednd.js');
  use_javascript('jqgrid/plugins/jquery.contextmenu.js');

  $js = '<link rel="stylesheet" type="text/css" href="'.base_url().'css/jquery/jquery-ui.custom.css" />
    <link rel="stylesheet" type="text/css" href="'.base_url().'js/jqgrid/css/ui.jqgrid.css" />';
  
  return $js;
}

function format_grid_data($rows, $total, $offset, $limit, $id_field = 'id', $additional = array(), $columns = array(), $skip_id = false)
{
  $data = array(
    "page"    => $offset + 1,
    "total"   => ceil($total/$limit),
    "records" => $total,
    "rows"    => array()
  );
  
  foreach($rows as $key => $row)
  {
    if(!empty($columns))
    {
      $row = grid_array_intersect_keys($row, $columns);
    }
	
    $id = $row[$id_field];
    if($skip_id)
      unset($row[$id_field]);
    
    $cell = array_values($row);
    $row[$id_field] = $id;
    
    if(!empty($additional))
    {
      foreach($additional as $add)
      {
        $cell[] = parse_dynamic_vars($add, $row);
      }
    }
    
    $data['rows'][] = array(
      "id"   => $id,
      "cell" => $cell
    );
  }
  
  return $data;
}

function grid_array_intersect_keys($row, $columns)
{
  $data = array();
  
  foreach($columns as $column)
  {
    $data[$column] = $row[$column];
  }
  
  return $data;
}

function parse_grid_params()
{  
  $data = array();
  $data['grid_page'] = isset($_REQUEST['page']) ? $_REQUEST['page'] - 1 : 0;
  $data['grid_limit'] = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;
  $data['grid_column'] = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id';
  $data['grid_order'] = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
  $data['grid_offset'] = $data['grid_page'] * $data['grid_limit'];
  
  return $data;
}

function grid_column_configuration($type, $item = '', $role = 'view')
{
  $additional = array();
  $columns = array();

  if($type == 'report')
  {
    $additional = array(
      '<a rel="'.$item.'|{id}" href="javascript:;" class="view-report">View Report</a>'
    );
    
    if($item == 'product')
      $columns = array('id', 'qa_product_id', 'qa_product_title');
    else
      $columns = array('id', 'qa_'.$item.'_id', 'qa_'.$item.'_name');
  }
  
  return array($additional, $columns);
}

function grid_moderate_configuration($type, $role)
{
  $additional = array('---');
  
  if($role != 'view')
  {
    $additional = array(
      moderate_select_tag().'&nbsp;<input type="button" rel="{qa_post_id}" class="update" value="Update" />'
    );
  }
    
  $columns = array('qa_post_id', 'qa_title', 'qa_description');
  
  return array($additional, $columns);
}

