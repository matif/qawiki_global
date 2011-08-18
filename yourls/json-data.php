<?php

require_once( dirname(__FILE__).'/includes/load-yourls.php' );

function get_url_stats($item_id, $item_type, $start_date, $end_date, $action_types)
{
  global $ydb;

  $table = YOURLS_DB_TABLE_URL;

  $query = "
    SELECT DATE(click_time) as clicks_date, count(yl.click_id) as clicks, ".(count($action_types) > 0 ? 'u.action_type' : "'visits'")." as action_type
    FROM `$table` u
    INNER JOIN ".YOURLS_DB_TABLE_LOG." yl on u.keyword = yl.shorturl
  ";
  
  $query .= "WHERE DATE(`click_time`) BETWEEN '$start_date' AND '$end_date'";
  
  if($item_type == 'store')
  {
    $query .= " AND `store_id` = $item_id";
  }
  else
  {
    $query .= " AND `item_id` = $item_id AND `item_type` = '$item_type'";
  }

  if(count($action_types) > 0)
  {
    foreach($action_types as &$action)
    {
      $action = "u.action_type = '".$action."'";
    }

    $query .= ' AND ('.join(' OR ', $action_types).')';
  }

  $query .= " GROUP BY clicks_date".(count($action_types) > 0 ? ', u.action_type' : '' );

  $rows = $ydb->get_results($query);
  
  return $rows;
}

$item_id = $_GET['item_id'];
$item_type = $_GET['item_type'];
$start_date = date('Y-m-d', $_GET['start_date']);
$end_date = date('Y-m-d', $_GET['end_date']);

$action_types = array();
if($_GET['action_types'] != 'all')
  $action_types = explode('|', trim($_GET['action_types']));

$data = get_url_stats($item_id, $item_type, $start_date, $end_date, $action_types);

echo json_encode($data);