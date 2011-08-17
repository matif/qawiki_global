<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of qa_brand
 *
 * @author purelogics
 */
class qa_brand extends Model
{
  //put your code here
  function qa_brand()
  {
    parent::Model();
  }

  function addBrand($data)
  {
    $this->db->insert('qa_brand', $data);
    return $this->db->insert_id();
  }

  function brand_exists($store_id, $id)
  {
    $this->db->select('id');
    $this->db->where('qa_brand_id', $id);
    $this->db->where('qa_store_id', $store_id);

    $result = $this->db->get('qa_brand')->result_array();

    return (isset($result[0]))?$result:NULL;
  }
  
  function getBrand($store_id, $offset = 0, $limit = 10, $sort_column = 'id', $sort_order = 'asc', $alpha_filter = '')
  {
    $this->db->select('*');
    $this->db->where('qa_store_id', $store_id);
    
    if(trim($alpha_filter))
    {
      $this->db->where('qa_brand_name like "'.$alpha_filter.'%"');
    }
    
    $this->db->limit($limit , $offset);
    $this->db->orderby($sort_column.' '.$sort_order);

    return $this->db->get('qa_brand')->result_array();
  }
  
  function getBrandCount($id, $filter_text = '')
  {
    $this->db->where('qa_store_id', $id);

    if(trim($filter_text) && $filter_text != -1)
    {
      $this->db->where('qa_brand_name LIKE "'.$filter_text.'"');
    }

    return $this->db->count_all_results('qa_brand');
  }

  function getDetails($store_id, $brand_id)
  {
    $query = "
      SELECT s.*, b.id, b.qa_brand_id as item_id, b.qa_brand_name as item_name, t.team_name, t.qa_team_id
      FROM qa_brand b
      INNER JOIN qa_store s ON b.qa_store_id = s.qa_store_id
      INNER JOIN qa_team t ON t.qa_store_id = s.qa_store_id
      WHERE b.qa_store_id = " . $store_id . "
        AND b.id = ".$brand_id;

    $res = $this->db->query($query)->result_array();

    return count($res) > 0 ? $res[0] : null;
  }
  function getModerateBrand($store_id, $offset,$limit, $params,$sort_column = 'id', $sort_order = 'asc')
  {
    $query = 'SELECT *
      FROM qa_brand AS b
      INNER JOIN qa_post AS q ON b.id = q.qa_ref_id
      WHERE q.qa_post_type = "brand"
        AND b.qa_store_id = '. $store_id;
      $query .= $params;
      $query .= ' GROUP BY b.id
      ORDER BY b.'.$sort_column.' '.$sort_order.
      ' LIMIT  '. $offset .','. $limit;

    $result = $this->db->query($query)->result_array();
    
    return $result;
  }

  function getWidgetBrands($store_id, $offset = 0, $limit = 50)
  {
    $this->db->select('b.*, count(p.id) as products_count');
    $this->db->where('b.qa_store_id', $store_id);
    $this->db->join('qa_product p', 'b.id = p.qa_brand_id AND p.qa_brand_id <> 0', 'left');
    $this->db->group_by('b.qa_brand_id');
    $this->db->having('products_count > 0');
    $this->db->limit($limit , $offset);
    $data = $this->db->get('qa_brand b')->result_array();

    return $data;
  }
  function getBrandById($store_id,$id)
  {
    $this->db->select('*');
    if(is_array($id))
      $this->db->where_in('id', $id);
    else
      $this->db->where('id', $id);
    $this->db->where('qa_store_id',$store_id);
    return $this->db->get('qa_brand')->result_array();
  }
  function updateBrandById($id , $data)
  {
    $this->db->where('id',$id);
    $this->db->update('qa_brand',$data);
  }

  function deleteBrandByid($store_id,$brand_id)
  {
    $this->db->where('id',$brand_id);
    $this->db->where('qa_store_id',$store_id);
    $this->db->delete('qa_brand');
  }

  function check_brand_map($association, $value,$store_id)
  {
    $this->db->select("id");
    $this->db->where("qa_store_id",$store_id);
    $this->db->where_in($association,$value);
    $this->db->group_by($association);
    return $this->db->get("qa_brand")->result_array();
    
  }
  
  function getBrandsByStoreId($store_id, $key)
  {    
    $this->db->select("id, qa_brand_name as Value, url");
    $this->db->like("qa_brand_name",$key);
    $this->db->where("qa_store_id", $store_id);       
    return $this->db->get("qa_brand")->result_array();
  }
}