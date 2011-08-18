<?php


/**
 *
 * @package    Charts
 *
 * @author     - Kashif Ali
 *
 */
class Charts extends Controller
{
  function Charts()
  {
    parent::Controller();

    $this->load->library('dateRange');

    $this->uid = $this->session->userdata('uid');
    $this->is_admin = $this->session->userdata('is_admin');
  }

  /**
   * function clicks
   *
   * @param <string>  $item_type
   * @param <int>     $item_id
   * @param <date>    $start_date
   * @param <date>    $end_date
   * @param <string>  $action_type
   *
   * return json data configured for graph
   *
   */
  function clicks($item_type, $item_id, $start_date = 0, $end_date = 0, $action_types = '', $debug = false)
  {
    if(!trim($action_types))
      $action_types = 'all';

    list($start_date, $end_date) = $this->daterange->validateDates($start_date, $end_date);

    $url = $this->config->item('shorten_url').'json-data/'.$item_id.'/'.$item_type.'/'.$start_date.'/'.$end_date.'/'.$action_types;
    if($debug) echo $url.'<br/>';

    // get clicks data
    $data = file_get_contents($url);
    
    $data = json_decode($data, true);

    $rows = array();
    $min_date = 0;
    $max_date = 0;
    $types = array();

    // arrange data
    if(count($data) > 0)
    {
      $types = array();
      foreach($data as $key => $value)
      {
        $value['clicks_date'] = $value['clicks_date'].' 06:00:00';
        $rows[$value['action_type']][strtotime($value['clicks_date'])] = intval($value['clicks']);
        $keys[] = strtotime($value['clicks_date']);

        if(!in_array($value['action_type'], $types))
          $types[] = $value['action_type'];
      }
      
      $keys = array_unique($keys);
      sort($keys);
      
      $min_date = $keys[0];
      $max_date = $keys[count($keys) - 1];

      while($max_date >= $min_date)
      {
        foreach($types as $type)
        {
          if(!isset($rows[$type][$max_date]))
          {
            $rows[$type][$max_date] = 0;
          }
        }

        $max_date = strtotime('-1 day', $max_date);
      }
    }

    // format data for graph
    $graph_data = array();
    $interval = 24 * 3600 * 1000;
    $colors = array('#CC0000', '#0072BC', '#00620C', '#7D26CD');

    foreach($types as $key => $type)
    {
      ksort($rows[$type]);

      $graph_data[] = array(
        'name'          =>  $type,
        'data'          =>  array_values($rows[$type]),
        'pointInterval' =>  $interval,
        'type'          =>  'area',
        'pointStart'    =>  $min_date * 1000,
        'color'         =>  $colors[$key]
      );
    }
    echo json_encode(array(
      'data'    => $graph_data,
      'y_title' => 'Clicks',
      'x_title' => 'Date'
    ));
    exit();
  }
  
  
}
