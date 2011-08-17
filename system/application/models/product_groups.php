<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of product_groups
 *
 * @author purelogics
 */
class product_groups extends Model {
  //put your code here
  function product_groups()
  {
    parent::Model();
  }

  function addProductGroup($data)
  {
    if(!$this->product_exists($data))
    {
      $this->db->insert('product_groups', $data);
    }
  }

  function product_exists($data)
  {
    $this->db->where('qa_group_id', $data['qa_group_id']);
    $this->db->where('qa_product_id', $data['qa_product_id']);
    $this->db->limit(1);
    $result = $this->db->get('product_groups')->result_array();

    if(!$result || count($result) == 0)
    {
      return false;
    }
    return true;
  }
  function getGroupProducts($id)
  {
    $sqlQuery = 'SELECT * FROM product_groups gp INNER JOIN qa_product p ON p.id = gp.qa_product_id
                 WHERE gp.qa_group_id ='.$id;
    return $this->db->query($sqlQuery)->result_array();
  }
}
?>
