<?php

class Products_csv
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
  
  function process_row($data, &$check)
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
    elseif (isset($this->fields['product id']) && trim($data[$this->fields['product id']]))
    {      
      $data = array_map('trim', $data);      
      
      $product_exists = $this->CI->product->product_exists($this->store_id, $data[$this->fields['product id']]);
      
      // save brand
      $brand_id = $this->process_brand($data);
      
      // save category
      $category_id = $this->process_category($data);
      
      //mapping
      $map_id = null;
      if ($product_exists)
      {
        $map_id = $this->process_mapping($data, $brand_id, $category_id, $product_exists);
      }
      
      // save product
      $image_path = $this->save_image($data);

      // save product
      if (!$product_exists)
      {
        $product_id = $this->save_product($data, $brand_id, $category_id, $image_path);
        var_dump($product_id);

        if (isset($map_id[0]) && !$this->cron)
        {
          $this->products[] = array(
            'map_id'        => $map_id[0]['id'],
            'qa_map_id'     => $map_id[0]['qa_product_id'],
            'product_id'    => $product_id,
            'qa_product_id' => $data[$this->fields['product id']]
          );
        }
      }
      else
        $product_id = $product_exists;

      return $product_id;
    }
  }

 /**
  * function process_brand
  *
  * @param <array>   $data
  *
  */
  private function process_brand($data)
  {
    $brand_id  = 0;

    if (isset($this->fields['brand id']) && trim($data[$this->fields['brand id']]) && isset($this->fields['brand name']) && trim($data[$this->fields['brand name']]))
    {
      $brand_exists = $this->CI->brand->brand_exists($this->store_id, $data[$this->fields['brand id']]);

      if(!$brand_exists)
      {
        $save_brand = array(
          'qa_store_id'   => $this->store_id,
          'qa_brand_id'   => ($data[$this->fields['brand id']] != '') ? $data[$this->fields['brand id']] : 0,
          'qa_brand_name' => (isset($data[$this->fields['brand name']])) ? $data[$this->fields['brand name']] : '',
          'url'           => (isset($data[$this->fields['brand url']])) ? $data[$this->fields['brand url']] : NULL
        );

        $brand_id = $this->CI->brand->addBrand($save_brand);
      }
      else
      {
        $brand_id = $brand_exists[0]['id'];
      }
    }

    return $brand_id;
  }

 /**
  * function process_category
  *
  * @param <array>   $data
  *
  */
  private function process_category($data)
  {
    $category_id = 0;
    $parent_id = 0;
    
    if (isset($this->fields['category id']) && trim($data[$this->fields['category id']]) && isset($this->fields['category name']) && trim($data[$this->fields['category name']]))
    {
      $category_exists = $this->CI->category->category_exists($this->store_id, $data[$this->fields['category id']]);

      if(isset($this->fields['parent id']) && trim($data[$this->fields['parent id']]))
      {
         $parent_info = $this->CI->category->category_exists($this->store_id, $data[$this->fields['parent id']]);
         if($parent_info)
         {
           $parent_id = $parent_info[0]['id'];
         }
      }
      
      if(!$category_exists)
      {
        $save_category = array(
          'qa_store_id'      =>  $this->store_id,
          'qa_category_id'   =>  ($data[$this->fields['category id']] != NULL) ? $data[$this->fields['category id']] : 0,
          'qa_category_name' =>  isset($this->fields['category name']) && isset($data[$this->fields['category name']]) ? $data[$this->fields['category name']] : NULL,
          'url'              =>  isset($this->fields['category url']) && isset($data[$this->fields['category url']]) ? $data[$this->fields['category url']] : NULL,
          'qa_parent_id'     =>  $parent_id
        );
        
        $category_id = $this->CI->category->addCategory($save_category);
      }
      else
      {
        $category_id = $category_exists[0]['id'];
      }
    }

    return $category_id;
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
  * function process_mapping
  *
  * @param <array>   $data
  * @param <int>     $brand_id
  * @param <int>     $category_id
  * @param <int>     $product_exists
  *
  */
  private function process_mapping($data, $brand_id, $category_id, &$product_exists)
  {
    $map_id = $this->CI->product->checkProductReplacement($this->store_id, $brand_id, $category_id, $data[$this->fields['product id']], $data[$this->fields['title']], $data[$this->fields['description']]);

    if (isset($map_id[0]))
    {
      $product_exists = false;
    }

    return $map_id;
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
  private function save_product($data, $brand_id, $category_id, $image_path)
  {
    $save_product = array(
      'qa_store_id' => $this->store_id,
      'user_id'     => $this->user_id,
      'qa_product_id' => $data[$this->fields['product id']],
      'qa_product_title' => $data[$this->fields['title']],
      'qa_product_description' => $data[$this->fields['description']],
      'qa_brand_id' => $brand_id,
      'qa_category_id' => $category_id,
      'product_image' => $image_path,
      'product_url' => (isset($this->fields['product url']) && trim($this->fields['product url'])) ? $data[$this->fields['product url']] : '',
      'created_at'  => date('Y-m-d H:i:s')
    );
    return $this->CI->product->addProduct($save_product);
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