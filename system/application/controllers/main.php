<?php


class main extends Controller
{
  function main()
  {
    parent::Controller();
    
    if (!trim($this->session->userdata('uid')))
    {
      redirect('register');
    }

    $this->load->model('stores', 'store');
    
    $this->is_admin = $this->session->userdata('is_admin');
    $this->uid = $this->session->userdata('uid');
    
    $this->layout = 'new_layout';
    
    set_store_list($this->store, $this->uid);
    
    set_body_class();
  }

  function urlStats($store_id, $type, $id)
  {
    $data['type'] = $type;
    $data['id'] = $id;

    // get store data
    $store_data = $this->store->getStoreById($store_id);
    
    // sub links slot
    $this->store_slot = array(
      'store'           =>  $store_data[0],
      'sub_links'       =>  get_sub_links('settings'),
      'selected'        =>  ''
    );
    
    $this->load->view('main/urlStats', $data);
  }

  function noaccess()
  {
    $this->load->view('errors/noaccess');
  }

  function getStores($term,$type='')
  {
    $this->load->model('stores','store');
    
    $data = $this->store->getStoresByName($this->is_admin, $this->session->userdata('uid'),$term,$type);
    if($data != null)
    {
      echo json_encode($data);
    }
    else
    {
      echo -1;
    }

    exit;
  }

}