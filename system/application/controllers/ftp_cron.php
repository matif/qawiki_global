<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ftp_cron
 *
 * @author purelogics
 */
class ftp_cron extends Controller {

  //put your code here
  function ftp_cron() {
    parent::Controller();
    $this->load->model('stores', 'store');
    $this->load->model('qa_product', 'product');
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_catagory', 'category');
    $this->uid = $this->session->userdata('uid');
  }

  function uploadProducts($offset = 0)
  {
    require_once(APPPATH.'libraries/products_csv.php');
    
    $limit = 10;

    do
    {
      $results = $this->store->get_ftp_stores($offset, $limit);
      print_r($results);
      if($results)
      {
        foreach($results as $result)
        {          
          
          $file_name = $this->config->item('root_dir').'/ftp_user/'.$result['qa_store_id'].'/'.$result['ftp_file_name'];

          echo $file_name.'<br/>';

          if (file_exists($file_name))
          {
            $product_csv = new Products_csv($result['qa_store_id'], $result['qa_user_id'], $file_name, true);
            $product_csv->process();        
          }
        }
      }

      $offset += $limit;
    }
    while($results);
    
    exit('DONE!!!');
  }

}

?>
