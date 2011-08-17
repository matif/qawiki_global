<?php


function format_custom_report($stats, $fields)
{
  $result = array();
  
  foreach($stats as $stat)
  {
    foreach($fields as $field)
    {
      $result[$field][] = $stat[$field];
    }
  }
  
  return $result;
}

function format_report_fields($mapping)
{
  $result = array();
  
  foreach($mapping as $map)
  {
    $result[$map['id']] = $map['title'];
  }
  
  return $result;
}