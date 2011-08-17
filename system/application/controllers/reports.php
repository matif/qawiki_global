<?php

/**
 *
 * @package Reports
 *
 * @author    - kashif
 *
 */

class Reports extends qaController
{
  function Reports()
  {
    parent::__construct();

    $this->load->model('qa_product','product');
    $this->load->model('qa_brand','brand');
    $this->load->model('qa_catagory','category');
    $this->load->library('dateRange');
  }

  function index($store_id = '', $item_type = '')
  {
    $data_view = array();
    $this->store_id = $store_id;
    $data_view['store_id'] = $store_id;
    $data_view = array_merge($data_view, parse_grid_params());
    

    if(trim($store_id))
    {
      $store_data = $this->store->getStoreById($store_id);
      Permissions::can_edit($store_data[0]->qa_store_id, $this->uid);      
      $start_date = 0; 
      $end_date = 0; 
      $this->date = list($start_date, $end_date) = $this->daterange->validateDates($start_date, $end_date);
      
      $this->store_slot = array(
        'store'            =>  $store_data[0],
        'sub_links'        =>  get_sub_links('settings'),
        'selected'         =>  'reports',
        'inner_links'      =>  get_inner_links_array('reports'),
        'inner_selected'   =>  (trim($item_type) ? ucFirst($item_type) : 'View Report')
      );
      
      $data_view['category_params'] = parse_pagination_params();
      
      // categories
      if($item_type == 'categories')
      {
        $data_view["categories"] = $this->category->getCategory($store_id, $data_view['category_params']['offset'], $data_view['category_params']['rec_per_page'] );
        $data_view['category_count'] = $this->category->getCategoryCount($store_id);
      
        $data_view['category_params']['total_records'] = $data_view['category_count'];
        pagination_calculate_pages($data_view['category_params']);
      }
      
      // brands
      if($item_type == 'brands')
      {
        $data_view["brands"] = $this->brand->getBrand($store_id, $data_view['category_params']['offset'], $data_view['category_params']['rec_per_page'] );
        $data_view['brands_count'] = $this->brand->getBrandCount($store_id);
        
        $data_view['brand_params'] = parse_pagination_params();
        $data_view['brand_params']['total_records'] = $data_view['brands_count'];
        pagination_calculate_pages($data_view['brand_params']);
      }
      
      // products
      if($item_type == 'products')
      {
        $data_view["products"] = $this->product->getProduct($store_id, $data_view['category_params']['offset'], $data_view['category_params']['rec_per_page'] );
        $data_view['products_count'] = $this->product->getProductCount($store_id);
        
        $data_view['product_params'] = parse_pagination_params();
        $data_view['product_params']['total_records'] = $data_view['products_count'];
        pagination_calculate_pages($data_view['product_params']);      
      }
    }
    
    $this->load->view('reports/index', $data_view);
  }
  
  /**
   * function categories
   * param <int> store_id
   */
  function categories($store_id)
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'categoryPag';

    // get records and total count
    $categories = $this->category->getCategory($store_id, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->category->getCategoryCount($store_id);
    
    pagination_calculate_pages($params);    
        
    $data['data'] = $this->load->view('reports/categories', array('categories' => $categories), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }
  
  /**
   * 
   * function brands
   * param <int> store_id
   * 
   */
  
  function brands($store_id)
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'brandPag';

    // get records and total count
    $brands = $this->brand->getBrand($store_id, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->brand->getBrandCount($store_id);
    
    pagination_calculate_pages($params);
        
    $data['data'] = $this->load->view('reports/brands', array('brands' => $brands), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }
  
  
  /**
   * function products
   * @param <int> $store_id
   * @param <int> $item_id
   * @param <string> $item_type 
   */
  function products($store_id, $item_id = null, $item_type = null)
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'productPag';

    // get records and total count
    if(!$item_id)
    {
      $products = $this->product->getProduct($store_id, $params['offset'], $params['rec_per_page']);
      $params['total_records'] = $this->product->getProductCount($store_id);
    }
    else
    {
      if($item_type == 'category')
      {
        $products = $this->product->getProductByCategoryId($item_id, $params['offset'], $params['rec_per_page']);
        $params['total_records'] = $this->product->getCountProductByCategoryId($item_id, $params['offset'], $params['rec_per_page']);
      }
      else
      {
        $products = $this->product->getProductByBrandId($item_id, $params['offset'], $params['rec_per_page']);
        $params['total_records'] = $this->product->getCountProductByBrandId($item_id, $params['offset'], $params['rec_per_page']);
      }
    }
    
    pagination_calculate_pages($params);
        
    $data['data'] = $this->load->view('reports/products', array('products' => $products), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }  
}