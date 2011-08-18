<?php

class Trigger extends Controller
{
  function __construct()
  {
    parent::Controller();
  }
  
  function turnOffEmail()
  {
    $this->load->model('posts');
    
    $tk = $_REQUEST['tk'];
    $next = $_REQUEST['next'];
    
    $tk = base64_decode($tk);
    $tk = explode(':|:', $tk);
    
    if(count($tk) == 4)
    {
      $data = array('email_opt_in' => 0);
    
      $this->db->where('qa_post_id', $tk[2]);
      $this->db->where('qa_post_type', $tk[1]);
      $this->db->where('qa_ref_id', $tk[0]);
              
      $this->db->update('store_item_posts', $data);
    }
    
    redirect($next);
    
    exit;
  }
}
