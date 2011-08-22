<?php

/**
 * 
 * @package  -  store_items
 *
 * @author   -  purelogics
 * 
 * 
 */
class store_items extends Model 
{
  
  private static $tableName = 'store_items';
  
  //put your code here
  function __construct()
  {
    parent::Model();
  }
  
  function addCsvProduct($data)
  {
    $this->db->insert('store_items', $data);
    return $this->db->insert_id();
  }
  
  
  function addProduct($data)
  {
    $this->db->insert('store_items', $data);
    return $this->db->insert_id();
  }
  
  function product_exists($store_id , $id)
  {
    $this->db->where('id', $id);
    $this->db->where('store_id', $store_id);
    $result = $this->db->get('store_items')->result_array();

    if(!$result || count($result) == 0)
    {
      return false;
    }
		
    return $result[0]['id'];
  }
  
  /**
   * 
   * function getItems
   * 
   * 
   * @param <int>        $store_id
   * @param <int>        $offset
   * @param <int>        $limit
   * @param <boolean>    $all
   * @param <string>     $sort_column
   * @param <string>     $sort_order
   * 
   * return list of items
   * 
   */
  function getItems($store_id, $item_type, $offset = 0, $limit = 10, $all = false, $sort_column = 'id', $sort_order = 'asc')
  {
    $this->db->where('store_id', $store_id);

    if($item_type)
    {
      $this->db->where('item_type', $item_type);
    }

    $this->db->orderby($sort_column.' '.$sort_order);

    $this->db->offset($offset);
    $this->db->limit($limit);    
    
    
    return $this->db->get(self::$tableName)->result_array();
  }

  /**
   * 
   * function getItemsCount
   * 
   * 
   * @param <int>        $store_id
   * @param <boolean>    $all
   * @param <string>     $filter_text
   * 
   * return count of items
   * 
   */
  function getItemsCount($store_id, $item_type, $all = false, $filter_text = '')
  {
    $this->db->select('COUNT(id) as CNT');
    $this->db->where('store_id', $store_id);

    if($item_type)
    {
      $this->db->where('item_type', $item_type);      
    }

    if(trim($filter_text) && $filter_text != -1)
    {
      $this->db->where('title LIKE "'.$filter_text.'"');
    }

    $result = $this->db->get(self::$tableName)->result_array();

    return $result[0]['CNT'];
  }

  function getById($store_id, $product_id)
  {
    $this->db->where('store_id', $store_id);
    $this->db->where('id', $product_id);
    $this->db->limit(1);
    $result = $this->db->get('store_items')->result_array();    
    return ($result) ? $result[0]['id'] : null;
  }
  function checkProductReplacement($store_id, $brand_id, $category_id, $upc_code, $title, $description){
    $this->db->select('id');
   // $this->db->select('qa_product_id');
    $this->db->where('store_id', $store_id);

   /* if(trim($brand_id))
      $this->db->where('qa_brand_id', $brand_id);

    if(trim($category_id))
      $this->db->where('qa_category_id', $category_id);
    */
    $this->db->where('id', $upc_code);
    $this->db->where('(title != "'.$title.'" OR description != "'.$description.'")');
    
    return $this->db->get('store_items')->result_array();

  }
  function deleteProductById($id) {
    $this->db->where('id', $id);
    $this->db->delete('store_items');
  }

  function getDetails($store_id, $product_id)
  {
    $query = "
      SELECT s.image_option, s.vote_type, s.qa_who_can_comment, s.qa_store_name, s.qa_permission, s.moderation_type, s.video_option, s.store_id,
        p.description as item_description, p.title as item_name, p.id as item_id, p.id, p.linked_id,
        t.team_name, t.qa_team_id,s.qa_threshold
      FROM store_items p
      INNER JOIN qa_store s ON p.store_id = s.qa_store_id
      INNER JOIN teams t ON t.qa_store_id = s.qa_store_id
      WHERE p.qa_store_id = " . $store_id . "
        AND p.id = ".mysql_escape_string($product_id);

    $res = $this->db->query($query)->result_array();

    return count($res) > 0 ? $res[0] : null;
  }
  function getProductByCategoryId($id,$offset = -1,$limit = -1)
  {
    $this->db->select('*');
    $this->db->where('item_type', $id);
    if($offset != -1)
      $this->db->limit($limit, $offset);
    $result = $this->db->get('store_items')->result_array();            
    return $result;
  }
  function getCountProductByCategoryId($id)
  {
    
    $this->db->where('item_type', $id);
    $result = $this->db->from('store_items');
    return $this->db->count_all_results();
  }
  function getProductByBrandId($id, $offset = -1, $limit = -1) {
    $this->db->select('*');
    $this->db->where('item_type', $id);
    if($offset != -1)
    $this->db->limit($limit, $offset);
    $result = $this->db->get('store_items')->result_array();
    
    return $result;
  }
  function getCountProductByBrandId($id){
    $this->db->select('*');
    $this->db->where('item_type', $id);    
    $this->db->from('store_items');    
    return $this->db->count_all_results();;
  }
  function getModerateProduct($store_id, $offset,$limit,$params,$sort_column = 'id', $sort_order = 'asc')
  {    
    $query = 'SELECT *
      FROM store_items AS p
      INNER JOIN store_item_posts AS q ON p.id = q.qa_ref_id
      WHERE q.qa_post_type = "product"
        AND p.store_id = '. $store_id;
      $query .= $params;
      $query .= ' GROUP BY p.id
      ORDER BY p.'.$sort_column.' '.$sort_order.
      ' LIMIT  '. $offset .','. $limit;

    $result = $this->db->query($query)->result_array();
    
    return $result;
  }
  function updateProduct($id, $data)
  {
    $this->db->where('id',$id);
    $this->db->update('store_items',$data);  
  }
  function getProductById($store_id, $product_id)
  {
    $this->db->where('store_id', $store_id);
    $this->db->where('id', $product_id);
    $this->db->limit(1);
    $result = $this->db->get('store_items')->result_array();
    return ($result) ? $result[0] : null;
  }

  function getAutonomousProductsCount($store_id)
  {
    $query = "
      SELECT COUNT(id) as CNT
      FROM `store_items`
      WHERE `store_id` = $store_id";

    $result = $this->db->query($query)->result_array();

    return $result[0]['CNT'];
  }

  function getAutonomousProducts($store_id)
  {
    $this->db->select('*');
    $this->db->where('store_id', $store_id);
  //  $this->db->where('qa_category_id', 0);
  //  $this->db->where('qa_brand_id', 0);
    $result = $this->db->get('store_items')->result_array();

    return $result;
  }
  function deletePostByProductId($product_id)
  {
    $this->db->where('qa_ref_id',$product_id);
    $this->db->delete('store_item_posts');
  }
  
  /**
   * function getProductsForMapping
   * 
   * @param <int> $store_id
   * @param <int> $user_id
   * 
   * return list of products which can be mapped
   * 
   */
  
  function getProductsForMapping($store_id, $user_id, $use_time = true)
  {    
    $time = date('Y-m-d H:i:s', strtotime('-5 minutes', time()));
    
    $this->db->join('team_members as tm', 'tm.qa_user_id = store_items.user_id AND tm.role = "creator"');
    $this->db->where('store_id', $store_id);
    $this->db->group_by('id');
    $this->db->where('linked_id', 0);
    
    if($use_time)
    {
      //$this->db->where('created_at > "'.$time.'"'); 
    }
    
    return $this->db->get('store_items')->result_array();
  }
  
  /**
   * function saveProductMapping
   * 
   * @param <int> $product_id
   * @param <int> $map_id
   * 
   */
  function saveProductMapping($product_id, $map_id , $association = null)
  {   
    $this->db->set('linked_id', $map_id);
    $this->db->where('id', $product_id);
    
    $this->db->update('store_items');
  }
  
  /**
   * function searchProduct
   * 
   * @param <int> $store_id
   * @param <int> $search_key
   * 
   */
  function searchProduct($store_id, $search_key, $offset = 0, $limit = 10)
  {
    $this->db->where('store_id', $store_id);
    $this->db->where('MATCH(title) AGAINST("'.mysql_escape_string($search_key).'" IN BOOLEAN MODE)');
    
    //$this->db->offset($offset);
    //$this->db->limit($limit);
    
    return $this->db->get('store_items')->result_array();
  }
  function check_product_map($association, $value,$store_id,$user_id)
  {    
    $this->db->select($association);
    $this->db->where("linked_id",0);
    $this->db->where("store_id",$store_id);    
    $this->db->where_in($association,$value);    
    $this->db->group_by($association);
    return $this->db->get("store_items")->result_array();
  }
  
  function getProductsByStoreId($store_id,$key)
  {    
    $this->db->select("id, title as Value, product_url as url");
    $this->db->select("id");
    $this->db->where("store_id", $store_id);       
    $this->db->like("title",$key);
    return $this->db->get("store_items")->result_array();
  }
  

  function getInfo($product_id)
  {
    $this->db->where('id', $product_id);
    
    $this->db->limit(1);
    
    $result = $this->db->get('store_items')->result_array();
    
    return ($result) ? $result[0] : null;
  }
}



