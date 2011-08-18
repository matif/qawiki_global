<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ajax
 *
 * @author purelogics
 */
class ajax extends Controller 
{
    //put your code here
  function ajax()
  {
    parent::Controller();

    $this->load->model('qa_login', "qa_login");
    $this->load->model('qa_catagory', "category");
    $this->load->model('qa_login', "qa_login");
    $this->load->model('qa_product', "product");
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_catagory', 'category');
    $this->load->model('stores', "store");

    $this->load->library('pager');

    $this->uid = $this->session->userdata("uid");
    
    verify_logged_in_user();
    
    set_store_list($this->store, $this->uid);
    
    $this->layout = 'new_layout';
  }

  function listSubCategory($store_id,$category_id,$offset = 0)
  {
    $this->no_layout = true;
    $data_view['user_role'] = Permissions::can_edit($store_id, $this->uid);
    $data_view['store'] = $this->store->getStoreById($store_id);
    $data_view['permission'] = store_permissions_mapping($data_view['store'][0]->qa_permission);
    $data_category = $this->category->getSubCategories($store_id,$category_id, $offset, 10);          

    $this->count_category = 10;

    $data_view['category'] = $data_category;
    $data_view['sub_link'] = 1;
    $url = base_url() . 'ajax/listSubCategory/'.$store_id.'/'. $category_id  ;
    $data_view['category_pager'] = $this->pager->get_pagination($this->count_category, $offset, 10, 'getMoreCategories', $url);      
    $this->layout = 'empty';
    
    echo $this->load->view("product/categoryPagination", $data_view, true);
    exit;
  }

  function search($store_id)
  {
   $data = $this->load->view('product/search_popup_backend', array(), true);
    render_json_response($data);
  }

  function categories($store_id)
  {   
    $data['autonomous_count'] = $this->product->getAutonomousProductsCount($store_id);
    $data['categories'] = $this->category->getWidgetCategories($store_id);
    $data['brands'] = $this->brand->getWidgetBrands($store_id);
    $data = $this->load->view('widget/categories_list', $data, true);
    render_json_response($data);
  }

  function search_products($store_id)
  {
    $data['already_linked'] = explode(',', $this->input->post("qawiki_products"));
    $data['already_linked'] = array_map('trim', $data['already_linked']);

    if(trim($this->input->post("search")))
      $data['products'] = $this->product->searchProduct($store_id, $this->input->post("search"));

    $data = $this->load->view('product/search_products_backend', $data, true);
    echo $data;
    exit;
  }
  
  /**
   * Catgroies picker - list all categories within a store
   * 
   * 
   */
  function categoryPicker($store_id, $filter = 'none', $parent_id = false)
  {
    Permissions::can_edit($store_id, $this->uid);
    
    $data['store_id'] = $store_id;
    $data['filter'] = $filter;
    $data['title'] = 'Pick Category';
    $data['item_field'] = 'qa_category_name';
    $data['rel'] = 'categoryPicker/'.$store_id;
    $data['type'] = 'categories';
    $data['expand'] = true;
    $data['parent_id'] = $parent_id;
            
    $data['items'] = $this->category->getCategory($store_id, 0, 1000, 'qa_category_name', 'asc', ($filter == 'none' ? '' : $filter), true, true, $parent_id);
    
    echo $this->load->view('components/_picker', $data, true);
    exit;
  }

  /**
   * Brands picker - list all brands within a store
   * 
   * 
   */
  function brandPicker($store_id, $filter = 'none')
  {
    Permissions::can_edit($store_id, $this->uid);
    
    $data['store_id'] = $store_id;
    $data['filter'] = $filter;
    $data['title'] = 'Pick Brand';
    $data['item_field'] = 'qa_brand_name';
    $data['rel'] = 'brandPicker/'.$store_id;
    $data['type'] = 'brands';
    
    $data['items'] = $this->brand->getBrand($store_id, 0, 1000, 'qa_brand_name', 'asc', ($filter == 'none' ? '' : $filter));
    
    echo $this->load->view('components/_picker', $data, true);
    exit;
  }
  
  function getStorePosts($store_id, $type)
  {
    $key = $this->input->post('term');
    
    if($type == "category")
    {
      $json = $this->category->getCategoryByStoreId($store_id, $key);
    }
    elseif($type == "brand")
    {      
      $json = $this->brand->getBrandsByStoreId($store_id, $key);
    }
    elseif($type == "product")
    {
      $json = $this->product->getProductsByStoreId($store_id, $key);
    }
    
    render_json_response($json);
  }
  
  /**
   * 
   * function store_browse
   * 
   * 
   */
  function store_browse($store_id, $type = '', $filter = '')
  {
    $this->no_layout = true;
    
    if($type == "category")
    {
      $data['rows'] = $this->category->getCategory($store_id, 0, 1000, 'qa_category_name', 'asc', $filter, true, true);
      $data['item_field'] = 'qa_category_name';
    }
    else if($type =="brand")
    {
      $data['rows'] = $this->brand->getBrand($store_id, 0, 1000, 'qa_brand_name', 'asc', $filter);
      $data['item_field'] = 'qa_brand_name';
    }
    else if($type == "product")
    {
      $data['rows'] = $this->product->getProduct($store_id, 0, 1000, false, 'qa_product_title', 'asc', $filter);
      $data['item_field'] = 'qa_product_title';
    }
    
    $this->load->view('components/_listByName', $data);
  }
  
  /**
   * 
   * function sub_categories
   * 
   * 
   */
  function sub_categories($store_id, $category_id)
  {
    $this->no_layout = true;
    
    $data['category_id'] = $category_id;
    $data['rows'] = $this->category->getCategory($store_id, 0, 1000, 'qa_category_name', 'asc', '', true, true, $category_id);
    
    $this->load->view('components/_subList', $data);
  }
}
