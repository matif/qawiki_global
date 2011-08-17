<?php

/**
 * 
 * @package - advancedReport
 * 
 * 
 * @author - Kashif Ali
 * 
 */

class advancedReport extends qaController
{
  function __construct()
  {
    parent::__construct();

    $this->load->model('qa_product','product');
    $this->load->model('qa_brand','brand');
    $this->load->model('qa_catagory','category');
    $this->load->model('stats', 'stats');
    
    $this->load->helper('format');
    
    $this->load->library('dateRange');
  }
  
  function index($store_id = 0)
  {
    $data_view = array();
    $data_view['store_id'] = $store_id;

    if(trim($store_id))
    {
      Permissions::can_edit($store_id, $this->uid);
        
      $data_view['store_data'] = $this->store->getStoreById($store_id);
      
      $this->store_slot = array(
        'sub_links'   =>  get_sub_links('settings'),
        'selected'    =>  'reports',
        'sub_heading' =>  'Report',
        'store'       =>  $data_view['store_data'][0]
      );
      
      $data_view['categories'] = $this->category->getCategory($store_id, 0, 200, 'qa_category_name', 'asc', '', true);
      $data_view['brands'] = $this->brand->getBrand($store_id, 0, 200);
    }
    else
      redirect ('dashboard');
    
    $this->load->view('advancedReport/index', $data_view);
  }
  
  function generate($store_id)
  {
    $data_view = array();
    $data_view['store_id'] = $store_id;

    if(trim($store_id))
    {
      Permissions::can_edit($store_id, $this->uid);
        
      $data_view['store_data'] = $this->store->getStoreById($store_id);
      
      $this->store_slot = array(
        'sub_links'   =>  get_sub_links('settings'),
        'selected'    =>  'reports',
        'sub_heading' =>  'Report',
        'store'       =>  $data_view['store_data'][0]
      );
    }
    
    // get values from post
    $category_ids = $this->input->post('categories');
    $brand_ids = $this->input->post('brands');
    $this->fields = $this->input->post('fields');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    
    list($start_date, $end_date) = $this->daterange->validateDates($start_date, $end_date);
    
    //if(!$this->fields)
    //  redirect('advancedReport/index/'.$store_id);
    
    // process stats
    $this->stats_data = $this->stats->getStats($category_ids, $brand_ids, $this->fields, $start_date, $end_date);
    $this->stats_data = format_custom_report($this->stats_data, $this->fields);
    
    $this->mapping = $this->config->item('report_fields');
    $this->mapping = format_report_fields($this->mapping);

    $this->load->view('advancedReport/generate', $data_view);
  }
  
  function testing()
  {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    
    $this->load->model('stats', 'stats');
   
    $this->stats->process();
    
    exit;
  }
}

