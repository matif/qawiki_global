<?php

/*
 * @package 
 * 
 */
class qaController extends Controller
{
  static $sort_column = '';
  
  function __construct()
  {
    parent::Controller();
    
    $this->load->model('stores', 'store');
    $this->load->model('posts', 'post');
    $this->load->model('qa_login', 'users');
    
    $this->load->model('store_items', 'store_items_m');
    $this->load->model('qa_brand', 'brand');
 //   $this->load->model('qa_catagory', 'category');
    $this->load->model('qa_teams', 'team');
    $this->load->model('qa_team_members', 'team_member');
    
    require_once APPPATH . 'models/post_history.php';
    
    $this->load->library('pager');
    
    $this->uid = $this->session->userdata('uid');
    $this->current_user_name = $this->session->userdata('name');
    $this->is_admin = $this->session->userdata('is_admin');
    
    // verify logged in user, redirect to signin if user not logged in
    verify_logged_in_user();
    
    // get stores list
    set_store_list($this->store, $this->uid);
    
    set_body_class();
    
    $this->layout = 'new_layout';
  }
  
  /**
   * 
   * Sorting functions
   * 
   * 
   */
  public static function sortMultiArray(array &$toSort, $sort_column, $type = 'string', $sort_order = 'desc')
  {
    self::$sort_column = $sort_column;
    if($type == 'string')
      uasort($toSort, array("qaController", "sort_compare_string_" . $sort_order));
    else
      uasort($toSort, array("qaController", "sort_numeric_" . $sort_order));
  }

  private static function sort_compare_string_desc($x, $y)
  {
    $result = strcmp(strtolower($x[self::$sort_column]), strtolower($y[self::$sort_column]));

    if($result == 0)
      return $result;
    else
      return $result * -1;
  }
  
  private static function sort_compare_string_asc($x, $y)
  {
    $result = strcmp(strtolower($x[self::$sort_column]), strtolower($y[self::$sort_column]));

    return $result;
  }

  private static function sort_numeric_desc($x, $y)
  {
    if($x[self::$sort_column] == $y[self::$sort_column])
      return 0;

    return ($x[self::$sort_column] > $y[self::$sort_column]) ? -1 : 1;
  }

  private static function sort_numeric_asc($x, $y)
  {
    if($x[self::$sort_column] == $y[self::$sort_column])
      return 0;

    return ($x[self::$sort_column] < $y[self::$sort_column]) ? -1 : 1;
  }
}

