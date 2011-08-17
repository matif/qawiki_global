<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of map_product
 *
 * @author purelogics
 */
class map_product extends Model {
    //put your code here
    function  map_product()
    {
      parent::Model();
    }
    function addMapping($data)
    {
      $this->db->insert('map_product', $data);
    }
    function getMappedProduts($store_id, $offset)
    {
      $this->db->where('qa_store_id', $store_id);
      $this->db->limit(10, $offset);
      return $this->db->get('map_product')->result_array();
    }
    function isMapped($s_id,$d_id,$store_id)
    {
      $this->db->where('source_id',$s_id);
      $this->db->where('destination_id',$d_id);
      $this->db->where('qa_store_id',$store_id);
      $result = $this->db->get('map_product')->result_array();
      if($result)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
}
?>
