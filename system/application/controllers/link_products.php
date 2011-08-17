<?php

class link_products extends Controller
{
  function link_products()
  {
    parent::Controller();

    // load models and libraries
    $this->load->model('qa_catagory', 'category');
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_product', 'product');
    $this->load->model('post_products', 'linked_products');

    $this->load->helper('widget');
    $this->load->helper('session');

    $this->uid = $this->session->userdata('uid');
  }

  /**
   * function categories
   *
   * return list of categories & brands
   *
   */
  function categories($store_id)
  {
    $data['autonomous_count'] = $this->product->getAutonomousProductsCount($store_id);
    $data['categories'] = $this->category->getWidgetCategories($store_id);
    $data['brands'] = $this->brand->getWidgetBrands($store_id);

    $data = $this->load->view('widget/categories_list', $data, true);

    render_json_response($data);
  }

  /**
   * function products
   *
   * return list of products
   *
   */
  function products($store_id, $ref_type, $ref_id, $parent = false)
  {    
    $data['already_linked'] = explode(',', $_REQUEST['qawiki_products']);
    $data['already_linked'] = array_map('trim', $data['already_linked']);

    if($ref_type == 'category')
    {
      if($parent)
      {
        $data['sub_categories'] = $this->category->getWidgetSubCategories($store_id, $ref_id);
        $response['sub_categories'] = $this->load->view('widget/sub_categories', $data, true);
      }

      $data['products'] = $this->product->getProductByCategoryId($ref_id);
    }
    elseif($ref_type == 'brand')
    {
      $data['products'] = $this->product->getProductByBrandId($ref_id);
    }
    else
    {
      $data['products'] = $this->product->getAutonomousProducts($store_id);
    }


    $data = $this->load->view('widget/products_list', $data, true);

    render_json_response($data);
  }
}