<?php

/*
 * @package grid
 * 
 * 
 * @author - Kashif
 * 
 */


class grid extends Controller
{
  function __construct()
  {
    parent::Controller();
    
  
    $this->uid = $this->session->userdata('user_id');
  }
  
  function index()
  {
    
    
    $this->load->view('components/grid');
  }
  
  function data()
  {
    $data = array(
      "page" => "1",
      "total" => 2,
      "records"=>"13",
      "rows"=>array(
        array(
          "id"=>"13",
          "cell"=>array("13","2007-10-06","Client 3","1000.00","0.00","1000.00","no tax at all")  
        )
      ),
      "userdata"=>array("amount"=>3220,"tax"=>342,"total"=>3564,"name"=>"Totals=>")
    );
    
    echo json_encode($data);
    exit;
  }
  
  function categories($store_id, $type = 'report')
  {
    $params = parse_grid_params();
    
    $this->load->model('qa_catagory','category');
    
    $categories = $this->category->getCategory($store_id, $params['grid_offset'], $params['grid_limit'], $params['grid_column'], $params['grid_order']);
    $count = $this->category->getCategoryCount($store_id);
    
    list($additional, $columns) = grid_column_configuration($type, 'category');
    
    $categories = format_grid_data($categories, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

    render_json_response($categories);
    
    exit;
  }
  
  function products($store_id, $type = 'report')
  {
    $params = parse_grid_params();
    
    $this->load->model('qa_product','product');
    
    $products = $this->product->getProduct($store_id, $params['grid_offset'], $params['grid_limit'], true, $params['grid_column'], $params['grid_order']);
    $count = $this->product->getProductCount($store_id, true);
    
    list($additional, $columns) = grid_column_configuration($type, 'product');
    
    $products = format_grid_data($products, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

    render_json_response($products);
    
    exit;
  }
  
  function brands($store_id, $type = 'report')
  {
    $params = parse_grid_params();
    
    $this->load->model('qa_brand','brand');
    
    $brands = $this->brand->getBrand($store_id, $params['grid_offset'], $params['grid_limit'], $params['grid_column'], $params['grid_order']);
    $count = $this->brand->getBrandCount($store_id);
    
    list($additional, $columns) = grid_column_configuration($type, 'brand');
    
    $brands = format_grid_data($brands, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

    render_json_response($brands);
    
    exit;
  }
  
  function questions($store_id, $post_id)
  {
    $params = parse_grid_params();
       

    render_json_response($brands);
    
    exit;
  }
}
