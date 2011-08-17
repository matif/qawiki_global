<?php


/**
 * 
 * @package - dashboard
 *
 * @author - Kashif
 * 
 */

class dashboard extends qaController
{
  function __construct()
  {
    parent::__construct();
    
    $this->load->library('pager');
  }
  
  /**
   * function index
   * 
   * @param <int> $store_id    store id
   * 
   * list all stores of a current user
   *
   */
  function index($type = "")
  { 
    $data_view = array();
    $data_view['store_params'] = parse_pagination_params();
    
    if(!$this->is_admin)
    {
      $memberData = $this->store->getMemeberStore($this->uid, $data_view['store_params']['offset'], $data_view['store_params']['rec_per_page']);
      
      $this->count = $this->store->getStoreCount($this->uid);
    }
    else
    {
      $memberData = $this->store->getAllStores($data_view['store_params']['offset'], $data_view['store_params']['rec_per_page']);
      
      $temp = array(
        'role'  => 'admin'
      );
     
      foreach($memberData as &$member)
      {
        $member = array_merge($member, $temp);
      }
      
      $this->count = $this->store->countAllStores();
    }
    
    $data_view['members'] = $memberData;
    
    $data_view['store_params']["total_records"] = $this->count;        
    
    pagination_calculate_pages($data_view['store_params']);
    
    if($type == "ajax")      
    {
      $data_view['store_params']['page_element_id'] = 'storePag';

      $data['data'] = $this->load->view('partials/_dashboard', $data_view, true);
      
      $data['pagination'] = $this->load->view('components/_pagination', $data_view['store_params'], true);
      
      render_json_response($data);
    }
    
    $this->load->view('stores', $data_view);
  }

  /**
   * function store_details
   * 
   * @param <int> $store_id    store id
   * 
   * store details
   *
   */
  function store_details($store_id)
  {
    $this->no_layout = true;
    
    $data_view['store_id'] = $store_id;
    $data_view['pending'] = $this->post->count_unmoderated_question($store_id);
    
    $data_view['chart_data'] = $this->post->get_per_month_question_volume($store_id);
    
    $this->load->view('store/details', $data_view);
  }
  
  
  
  function testHistory()
  {
    error_reporting(E_ALL);
    ini_set('display_error', true);
    
    Post_history::saveHistory(1000, 'question', 1, true);
    
    exit;
  }
  
  function get_stores()
  {
    
  }
}
