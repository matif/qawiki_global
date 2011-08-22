<?php

class qaCsv
{
  private $file_path;
  private $cron;
  private $error;
  private $store_id;
  private $user_id;
  private $CI;
  private $fields;
  private $save_images_locally;
  private $products = array();
	
  function __construct($store_id, $user_id, $file = '', $cron = false)
  {
    $this->store_id = $store_id;
    $this->user_id = $user_id;
    $this->file_path = $file;
    $this->cron = $cron;
    $this->CI =& get_instance();	
  } 

  function process()
  {
    $handle = fopen($this->file_path, "r");
    $check = 0;    
    // get store configuration
    $this->store_configuration();

    while (($data = fgetcsv($handle, 3000, ",")) !== FALSE)
    {
      $this->process_row($data, $check);
    }
    fclose($handle);
  }
  
  function store_configuration()
  {
    // get store configuration
    $this->save_images_locally = $this->CI->store->imageOption($this->store_id);
  }
  
  function process_row($data, $check)
  {   
	
    
    if ($check == 0)
    {
      $this->fields = array_map('trim', $data);      
      $this->fields = array_map('strtolower', $this->fields);
      
      $this->fields = array_flip($this->fields);      
 
	  // validate csv header
      validate_csv_header($this->fields, $data);
     
      if (isset($data['error']))
      {
        $this->error = $data['error'];
      }

      $check++;
      
    }
	elseif (isset($this->fields['item id']) && trim($data[$this->fields['item id']]))
    { 
	  $data = array_map('trim', $data);      
     
     // $product_exists = $this->CI->product->product_exists($this->store_id, $data[$this->fields['item id']]);
      
      // save product
      //$image_path = $this->save_image($data);

      // save product
      //if (!$product_exists)
	  if ($data)
      {
		
        //$product_id = $this->save_product($data, $brand_id, $category_id, $image_path);
	 	$product_id = $this->save_product($data);	
        var_dump($product_id);
      }
      else
        $product_id = $product_exists;

      return $product_id;
    }
  }


 /**
  * function save_image
  *
  * @param <array>   $data
  *
  */
  private function save_image($data)
  {
    $name = '';

    if (isset($this->fields['image url']) && trim($data[$this->fields['image url']]))
    {
      $name = save_product_image($data[$this->fields['image url']], $this->save_images_locally,$this->store_id);
    }

    return $name;
  }

  /**
  * function save_product
  *
  * @param <array>   $data
  * @param <int>     $brand_id
  * @param <int>     $category_id
  * @param <int>     $image_path
  *
  */
  private function save_product($data)
  {
	  
    $save_product = array(
		'store_id'	=> $this->store_id,
		'user_id'    	=> $this->user_id,
		'item_id' 	=> $data[$this->fields['item id']],
		'item_type' 	=> $data[$this->fields['item type']],
		'title' 	=> $data[$this->fields['title']],
		'description' 	=> $data[$this->fields['description']],
		'link_url' 	=>  (isset($this->fields['link url']) && trim($this->fields['link url'])) ? $data[$this->fields['link url']] : '',
		'image_url' 	=> $data[$this->fields['image url']],
		'parent_id' 	=> $data[$this->fields['parent id']],
      	'linked_id' 	=> '',
      	'created_at'  	=> date('Y-m-d H:i:s')
    );
    return $this->CI->store_items_m->addCsvProduct($save_product);
  }
  
  
 /**
  * function has_error
  *
  * return <boolean>
  */
  
  public function has_error()
  {
    return (!is_null($this->error));
  }

 /**
  * function get_error
  *
  * return <boolean>
  */
  
  public function get_error()
  {
    return $this->error;
  }

   /**
  * function get_products
  *
  * return <boolean>
  */
  public function get_products()
  {
    return $this->products;
  }

  public function checkAssociation($association)
  {
    
  }

}