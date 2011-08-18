<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of qa_catagory
 *
 * @author purelogics
 */
class qa_catagory extends Model
{
  //put your code here
  function qa_catagory()
  {
    parent::Model();
  }

  function addCategory($data)
  {
    $this->db->insert('qa_category', $data);
    return $this->db->insert_id();
  }

  function category_exists($store_id, $id)
  {
    $this->db->select('id');
    $this->db->where('qa_category_id', $id);
    $this->db->where('qa_store_id', $store_id);
    $result = $this->db->get('qa_category')->result_array();
    return (isset($result[0]) ? $result : NULL);
  }

  function getCategory($id, $offset = 0, $limit = 10, $sort_column = 'id', $sort_order = 'asc', $alpha_filter = '', $only_parent = 0, $count_sub = false, $parent_id = false)
  {
    $this->db->select('c.*');
    $this->db->where('c.qa_store_id', $id);
    
    if(trim($alpha_filter))
    {
      $this->db->where('c.qa_category_name like "'.$alpha_filter.'%"');
    }
    
    if($parent_id > 0)
    {
      $this->db->where('c.qa_parent_id', $parent_id);
    }
    elseif($only_parent)
    {
      $this->db->where('c.qa_parent_id', 0);
    }
    
    if($count_sub)
    {
      $this->db->select('COUNT(child.id) as cnt');
      $this->db->join('qa_category child', 'c.id = child.qa_parent_id', 'left');
      $this->db->groupby('c.id');
    }
    
    $this->db->orderby($sort_column.' '.$sort_order);
    
    $this->db->offset($offset);
    $this->db->limit($limit);

    //if(trim($filter_text) && $filter_text != -1)
    //{
      //$this->db->where('qa_category_name LIKE "'.$filter_text.'"');
    //}

    return $this->db->get('qa_category c')->result_array();
  }

  function getCategoryCount($id, $filter_text = '')
  {
    $this->db->select('*');
    $this->db->where('qa_store_id', $id);

    if(trim($filter_text) && $filter_text != -1)
    {
      $this->db->where('qa_category_name LIKE "'.$filter_text.'"');
    }

    $this->db->from('qa_category');

    return $this->db->count_all_results();
  }

   function getModerateCategory($store_id, $offset,$limit, $params,$sort_column = 'id', $sort_order = 'asc')
  {
    $query = 'SELECT *
      FROM qa_category AS c
      INNER JOIN store_item_posts AS q ON c.id = q.qa_ref_id
      WHERE q.qa_post_type = "category"
        AND c.qa_store_id = '. $store_id;
        $query .= $params;
      $query .= ' GROUP BY c.id
      ORDER BY c.'.$sort_column.' '.$sort_order.
      ' LIMIT  '. $offset .','. $limit;
    
    $result = $this->db->query($query)->result_array();
    
    return $result;
  }

  function getDetails($store_id, $category_id)
  {
    $query = "
      SELECT s.*, c.id, c.qa_category_id as item_id, c.qa_category_name as item_name, t.team_name, t.qa_team_id
      FROM qa_category c
      INNER JOIN qa_store s ON c.qa_store_id = s.qa_store_id
      INNER JOIN teams t ON t.qa_store_id = s.qa_store_id
      WHERE c.qa_store_id = " . $store_id . "
        AND c.id = ".$category_id;

    $res = $this->db->query($query)->result_array();

    return count($res) > 0 ? $res[0] : null;
  }

  function getWidgetCategories($store_id, $offset = 0, $limit = 50)
  {
    $this->db->select('c.*, count(p.id) as products_count, (CASE WHEN c.qa_parent_id = 0 THEN c.id ELSE c.qa_parent_id END) AS parent_id');
    $this->db->where('c.qa_store_id', $store_id);
    
    $this->db->join('qa_product p', 'c.id = p.qa_category_id AND p.qa_category_id <> 0', 'left');
    
    $this->db->group_by('parent_id');
    $this->db->having('products_count > 0');
    
    $this->db->limit($limit);
    $this->db->offset($offset);
    
    $data = $this->db->get('qa_category c')->result_array();
    
    return $data;
  }
  
  function getWidgetSubCategories($store_id, $parent_id, $offset = 0, $limit = 50)
  {    
    $this->db->select('c.*, count(p.id) as products_count');
    $this->db->where('c.qa_store_id', $store_id);
    $this->db->where('c.qa_parent_id', $parent_id);
    
    $this->db->join('qa_product p', 'c.id = p.qa_category_id AND p.qa_category_id <> 0', 'left');
    
    $this->db->group_by('c.qa_category_id');
    $this->db->having('products_count > 0');
    
    $this->db->limit($limit);
    $this->db->offset($offset);
    
    $data = $this->db->get('qa_category c')->result_array();
    
    return $data;
  }
  
  function getCategoryById($store_id,$id)
  {
    $this->db->select('*');
     if(is_array($id))
        $this->db->where_in('id', $id);
     else
        $this->db->where('id', $id);
     
    $this->db->where('qa_store_id',$store_id);
    return $this->db->get('qa_category')->result_array();
  }
  function updateCategoryById($id , $data)
  {
    $this->db->where('id',$id);
    $this->db->update('qa_category',$data);
  }

  function deleteCategoryByid($store_id,$category_id)
  {
    $this->db->where('id',$category_id);
    $this->db->where('qa_store_id',$store_id);
    $this->db->delete('qa_category');
  }
  function deleteCategoryProducts($store_id,$category_id,$type = 'category')
  {
    $this->db->where('qa_store_id',$store_id);
    if($type == 'category')
    {
      $this->db->where('qa_category_id',$category_id);
      $this->db->where('qa_brand_id',0);
    }
    else if($type == 'brand')
    {
      $this->db->where('qa_brand_id',$category_id);
      $this->db->where('qa_category_id',0);
    }
    
    $this->db->delete('qa_product');
  }
  function getSubCategories($store_id, $category_id,$offset,$limit = 10)
  {
    $this->db->where('qa_store_id',$store_id);
    $this->db->where('qa_parent_id',$category_id);
    $this->db->limit($limit);
    $this->db->offset($offset);
    return $this->db->get('qa_category')->result_array();
  }
  function check_category_map($association, $value,$store_id)
  {
    $this->db->select("id");
    $this->db->where("qa_store_id",$store_id);
    $this->db->where_in($association,$value);
    $this->db->group_by($association);
    return $this->db->get("qa_category")->result_array();    
  }
  
  function getCategoryByStoreId($store_id,$key)
  {    
    $this->db->select("id, qa_category_name AS Value, url");
    $this->db->like("qa_category_name",$key);
    $this->db->where("qa_store_id", $store_id);       
    return $this->db->get("qa_category")->result_array();
  }
}
