<?php

/**
 * 
 * @package  -  Catalog
 * 
 * @author  -  Kashif
 * 
 * 
 */
class Catalog extends qaController
{
  function __construct()
  {
    parent::__construct();
    
    $this->store_id = $this->uri->segment(3);
    $user_role = Permissions::can_edit($this->store_id, $this->uid);
    
    $this->store_data = $this->store->getStoreById($this->store_id);
  }
  
  /**
   * 
   * function index
   * 
   * @param <int>      $store_id
   * @param <string>   $item_type
   * 
   */
  function index($store_id, $item_type = '')
  {
    $this->store_slot = array(
      'store'            =>  $this->store_data[0],
      'sub_links'        =>  get_sub_links('settings'),
      'selected'         =>  'settings',
      'inner_links'      =>  get_inner_links_array('settings'),
      'inner_selected'   =>  'Catalog',
      'drop_down'        =>  $item_type
    );
    
    $data['items_params'] = parse_pagination_params();
    
    // get records and total count
    $data['items'] = $this->store_items_m->getItems($this->store_id, $item_type, $data['items_params']['offset'], $data['items_params']['rec_per_page']);
    $data['items_count'] = $this->store_items_m->getItemsCount($this->store_id , $item_type);

    // define pagination params
    $data['items_params']['total_records'] = $data['items_count'];
    pagination_calculate_pages($data['items_params']);
    
    $this->load->view('catalog/index', $data);
  }
  
  /**
   * 
   * function categories
   * 
   * 
   */
  function categories()
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'categoryPag';

    // get records and total count
    $categories = $this->category->getCategory($this->store_id, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->category->getCategoryCount($this->store_id);
    
    pagination_calculate_pages($params);    
        
    $data['data'] = $this->load->view('catalog/_categories', array('categories' => $categories), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }
  
  /**
   * 
   * function brands
   * 
   * 
   */
  function brands()
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'brandPag';

    // get records and total count
    $brands = $this->brand->getBrand($this->store_id, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->brand->getBrandCount($this->store_id);
    
    pagination_calculate_pages($params);
        
    $data['data'] = $this->load->view('catalog/_brands', array('brands' => $brands), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }
  
  /**
   * 
   * function products
   * 
   * 
   */
  function products($store_id, $item_type = null, $item_id = null)
  {
    $params = parse_pagination_params();
    $params['page_element_id'] = 'productPag';

		$products = $this->store_items_m->getItems($this->store_id, $item_type, $params['offset'], $params['rec_per_page']);
		$params['total_records'] = $this->store_items_m->getItemsCount($this->store_id, $item_type);
    
    pagination_calculate_pages($params);
        
    $data['data'] = $this->load->view('catalog/_items', array('items' => $products), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }
  
  /**
   * 
   * function editItem
   * 
   * 
   */
   
  function editItem($store_id, $item_id, $item_type)
  {
    $this->no_layout = true;
    
    $itemInfo = $this->store_items_m->getProductById($this->store_id, $item_id);
    
    if(isset($itemInfo[0]))
    {
      $itemInfo = $itemInfo[0];
    }
    
    $data['itemInfo'] = $itemInfo;
    $data['item_id'] = $item_id;
    $data['item_type'] = $item_type;
    
    $this->load->view('catalog/editItem', $data);
  }
   
 /* function editItem($store_id, $item_id, $item_type)
  {
    $this->no_layout = true;
    
    if($item_type == 'category')
    {
      $itemInfo = $this->category->getCategoryById($this->store_id, $item_id);
    }
    elseif($item_type == 'brand')
    {
      $itemInfo = $this->brand->getBrandById($this->store_id, $item_id);
    }
    else
    {
      $itemInfo = $this->product->getProductById($this->store_id, $item_id);
    }
    
    if(isset($itemInfo[0]))
    {
      $itemInfo = $itemInfo[0];
    }
    
    $data['itemInfo'] = $itemInfo;
    $data['item_id'] = $item_id;
    $data['item_type'] = $item_type;
    
    $this->load->view('catalog/editItem', $data);
  }*/
  
  /**
   * 
   * function saveEditItem
   * 
   * 
   */
   
  function saveEditItem()
  {
    $this->no_layout = true;
    
    $id			= $this->input->post('id');
    $item_type	= $this->input->post('item_type');
    
    $response = array(
      'item_id'      => trim($this->input->post('item_id')),
      'title'        => trim($this->input->post('itemTitle'))
    );
      $response['description']  = trim($this->input->post('itemDescription'));
      
      $data = array(
        'item_id'      => $response['item_id'],
        'title'        => $response['title'],
        'description'  => $response['description']
      );
      
      $this->store_items_m->updateProduct($id, $data);
    
    
    render_json_response($response);
  }
   
   
   
 /* function saveEditItem()
  {
    $this->no_layout = true;
    
    $item_id	= $this->input->post('item_id');
    $item_type	= $this->input->post('item_type');
    
    $response = array(
      'id'           => trim($this->input->post('itemRemoteId')),
      'title'        => trim($this->input->post('itemTitle'))
    );
    
    if($item_type == 'product')
    {
      $response['description']  = trim($this->input->post('itemDescription'));
      
      $data = array(
        'qa_product_id'           => $response['id'],
        'qa_product_title'        => $response['title'],
        'qa_product_description'  => $response['description']
      );
      
      $this->product->updateProduct($item_id, $data);
    }
    else
    {
      $data = array(
        'qa_'.$item_type.'_id'      => $response['id'],
        'qa_'.$item_type.'_name'    => $response['title']
      );
      
      if($item_type == 'category')
      {
        $this->category->updateCategoryById($item_id, $data);
      }
      else
      {
        $this->brand->updateBrandById($item_id, $data);
      }
    }
    
    render_json_response($response);
  }*/
  
  /**
   * 
   * function questionDialog
   * 
   * 
   */
  function questionDialog($store_id, $item_id, $item_type)
  {
  
    $this->no_layout = true;
    
    $data['item_id'] = $item_id;
    $data['item_type'] = $item_type;
    
    $this->load->view('catalog/questionDialog', $data);
  }
  
  /**
   * 
   * function saveQuestion
   * 
   * 
   */
  function saveQuestion()
  {
    $this->no_layout = true;
    
    $item_id = $this->input->post('item_id');
    $item_type = $this->input->post('item_type');
    
    if($item_id)
    {
      $question = array(
        'qa_store_id'     => $this->store_id,
        'qa_ref_id'       => $item_id,
        'qa_post_type'    => $item_type,
        'qa_user_id'      => $this->uid,
        'qa_title'        => trim($this->input->post('question-text')),
        'qa_parent_id'    => 0,
        'qa_created_at'   => gmdate('Y-m-d H:i:s'),
        'mod_status'      => 'valid'
      );
      
      $this->post->addPost($question);
    }
    
    exit('1');
  }
  
  function test()
  {
    require_once APPPATH . '/libraries/qaCssParser.php';
    
    $qaCssParser = new qaCssParser($this->store_id);
    $qaCssParser->set('text', array('text-decoration: underline'));
    $qaCssParser->set('action', array('color: #000'));
    $qaCssParser->set('a.qawiki-action', array('text-align: right'), false);
    
    $qaCssParser->save();
    
    exit;
  }
  
  /**
   *
   * @param type $store_id
   * function process_ftp_file 
   */
  
  function process_ftp_file($store_id, $file_name = "" )
  {
    require_once(APPPATH.'libraries/products_csv.php');    
		
    if(isset($_FILES["upload_csv"]) && $_FILES["upload_csv"]["tmp_name"] != "")
    {
			$dir_path = $file_name = $this->config->item('root_dir').'/ftp_user/'.$store_id."/";

      if(!file_exists($dir_path))
        mk_dir ($dir_path);     
      
      $dir_path .= $_FILES["upload_csv"]["name"];
      
      move_uploaded_file($_FILES["upload_csv"]["tmp_name"], $dir_path);      
      
      $product_csv = new Products_csv($store_id, $this->uid, $dir_path, true); 
      $product_csv->process();
			echo 1;
			exit;
    }
    if($file_name != "")
      $file_name = $this->config->item('root_dir').'/ftp_user/'.$store_id.'/'.$file_name.".csv";
    
    if ($file_name != "" || file_exists($file_name))
    {
      $product_csv = new Products_csv($store_id, $this->uid, $file_name, true);      
      $product_csv->process();        
      echo 1;
    }
    else
    {
      echo -1;      
    }
    exit;
  }
  
  /**
   *
   * @param <int> $store_id 
   * function name get_ftb_file_name
   */
  
  function get_ftb_file_name($store_id)
  {
    $store_info = $this->store->get_ftp_stores(0,1,$store_id);
    echo $store_info[0]["ftp_file_name"];
    exit;
  }  
}