<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of qa_brand
 *
 * @author kashif
 */
class post_products extends Model
{
  //put your code here
  function qa_products()
  {
    parent::Model();
  }

  function save_products($post_id, $products)
  {
    foreach($products as $product)
    {
      if(trim($product))
      {
        $this->db->insert('post_products', array(
          'post_id'    => $post_id,
          'product_id' => $product,
          'linked_at ' => gmdate('Y-m-d H:i:s')
        ));
      }
    }
  }

  function get_linked(&$questions)
  {
    $ids = array();
    foreach($questions as &$question)
    {
      $ids[] = $question['qa_post_id'];
    }

    if(!empty($ids))
    {
      $this->db->select('post_id, qa_product_title as title, product_image as image, product_url as url');
      $this->db->where_in('r.post_id', $ids);
      $this->db->join('qa_product p', 'r.product_id = p.id', 'inner');

      $products = $this->db->get('post_products r')->result_array();

      foreach($products as $product)
      {
        foreach($questions as &$question)
        {
          if(!isset($question['related']))
          {
            $question['related'] = array();
          }

          if($question['qa_post_id'] == $product['post_id'])
          {
            $question['related'][] = $product;
            break;
          }
        }
      }
    }
  }
  function get_linked_by_post_id($post_id)
  {
    $this->db->select('post_id, qa_product_title as title, product_image as image, product_url as url');
    $this->db->where('r.post_id', $post_id);
    $this->db->join('qa_product p', 'r.product_id = p.id', 'inner');
    $products = $this->db->get('post_products r')->result_array();
    return $products;
  }

  function deleteProducts($post_id)
  {       
    $this->db->where('post_id',$post_id);
    $this->db->delete('post_products');
  }
}

