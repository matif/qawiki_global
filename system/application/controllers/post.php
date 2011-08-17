<?php
/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

/**

 * Description of Post

 *

 * @author purelogics

 */
class Post extends qaController
{
  function Post()
  {
    parent::__construct();

    $this->load->library('form_validation');
    $this->load->library('validation');

    $this->load->model('qa_login', 'login');
    $this->load->model('post_products', 'linked_products');
    $this->load->model('qa_product', 'product');
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_catagory', 'category');
    $this->load->model('qa_teams', 'team');
    $this->load->model('qa_team_members', 'team_member');
    $this->load->model('groups', 'group');
    $this->load->model('product_groups', 'product_group');
    $this->load->model('team_invites', "invites");
    $this->load->model('map_product', 'map');
    
    $this->load->helper('widget');
    $this->load->helper('image_helper');
  }

  /**
   * function createStore
   *
   */
  function createStore($type='', $id='')
  {
    $this->layout = 'new_layout';

    if(trim($id))
    {
      $store_data = $this->store->getStoreById($id);
      Permissions::can_edit($store_data[0]->qa_store_id, $this->uid);

      $this->store_slot = array(
        'sub_heading'    =>  'Store Settings',
        'store'          =>  $store_data[0],
        'sub_links'      =>  get_sub_links('settings'),
        'inner_links'    =>  get_inner_links_array('store_edit'),
        'selected'       =>  'settings',
        'inner_selected' =>  'Store Settings'
      );
    }
    else
    {
      $this->store_slot = array(
        'sub_heading' => 'Step 1: Store Settings',
        'heading'     => 'Add Store',
        'sub_links'   =>  get_sub_links('create_store'),        
        'selected'    => 'settings'
      );
    }    
//    print_r($this->store_slot);
    if (trim($this->input->post('storeName') != ''))
    {
      $vote  = $this->input->post('vote', 1);
      $comment = $this->input->post('comment', 1);
      $image = $this->input->post('image', 1);
      $moderation = $this->input->post('moderation', 1);
      $video_option = $this->input->post('video_option', 1);
      $vote_type = $this->input->post('vote_type', 1);
      $product_image = $this->input->post('product_image', 1);
      
      $data = array(
        'qa_user_id'          => $this->session->userdata('uid'),
        'qa_store_name'       => $this->input->post('storeName'),
        'qa_who_can_vote'     => $vote,
        'qa_who_can_comment'  => $comment,
        'qa_permission'       => $this->input->post('premission'),
        'image_option'        => $image,
        'moderation_type'     => $moderation,
        'vote_type'           => $vote_type,
        'video_option'        => $video_option,
        'save_images_locally' => $product_image,
        'ftp_file_name'       => $this->input->post('ftp_file_name'),
        'qa_threshold'        => $this->input->post('threshold'),
        'cart_type'           => $this->input->post('cart_type')
      );

      if ($type == 'update')
      {
        $this->store->updateStore($id, $data);
        $team_settings = $this->session->userdata('team_settings');
        $store_data = $this->store->getStoreById($id);
//        redirect('post/webInfo/' . $id."/edit");
      }
      else
      {
        $this->store->addStores($data);
        $id = $this->db->insert_id();

        $data = array(
          'qa_store_id' => $id,
          'team_name' => 'default'
        );
        
        $role_id = triger_designation_milestones($id);        
        $teamId = $this->team->addTeam($data);
        
        $this->team_member->addOwnerAsMember($teamId, $this->session->userdata('uid'), $role_id);
        $team_member_id = $this->db->insert_id();        
        redirect('post/webInfo/' . $id);
      }
    }

    if ($type == 'edit' || $type == "update")
    {
      $data['store_info'] = $store_data;
      $this->load->view('createStore', $data);
    }
    else
    {
      $this->load->view('createStore');
    }
  }

  /**
   * function addPost
   *
   */
  function addPost($id)
  {    
    $user_role = Permissions::can_edit($id, $this->uid);
    $this->role = $user_role;
    if($user_role == 'view')
      redirect('moderate/index/'.$id);

    $this->layout = 'new_layout';
    $store_data = $this->store->getStoreById($id);

    $this->store_slot = array(
      'sub_heading'    =>  'Upload Products',
      'store'          =>  $store_data[0],
      'sub_links'      =>  get_sub_links('settings'),
      'inner_links'    =>  get_inner_links_array('store_edit'),
      'selected'       =>  'settings',
      'inner_selected' =>  'Upload Products'
    );

    $this->store_id = $id;
    $data = array();

    $this->session->set_userdata('products', null);

    /*if (isset($_FILES['file']))
    {   
      // save uploaded csv
      require_once(APPPATH.'libraries/products_csv.php');
      
      $this->product_csv = new Products_csv($this->store_id, $_FILES['file']['tmp_name']);
      $this->product_csv->process();
    
      if(!$this->product_csv->has_error())
      {
        $this->session->set_userdata('products', $this->product_csv->get_products());

        redirect('post/mapProducts/' . $this->store_id);
      }
      else
      {
        $data['error'] = $this->product_csv->get_error();
      }
    }*/
    
    $this->load->view('postProduct', $data);
  }

  /**
   * function formatPost
   *
   * @param <int> $store_id     store id
   * 
   */
  function formatPost($store_id)
  {
    
    $this->layout = 'new_layout';
    
    $user_role = Permissions::can_edit($store_id, $this->uid);
    
    if($user_role == 'view')
      redirect('post/showProduct/'.$store_id);
    
    $store_data = $this->store->getStoreById($store_id);
    
    $this->store_slot = array(
      'sub_heading'    =>  'Upload Products',
      'store'          =>  $store_data[0],
      'sub_links'      =>  get_sub_links('settings'),
      'inner_links'    =>  get_inner_links_array('store_edit'),
      'selected'       =>  'upload',
      'inner_selected' =>  'Upload Products'
    );
    
    
    
    $data = array();
    $data['store_id'] = $store_id;
    $path = $this->config->item('csv_upload_path');
    
    if (isset($_FILES['file']))
    {      
      if ($_FILES['file']['size'] < 6291456)
      {
        $data['csv'] = array();
        $data['filename'] = $_FILES['file']['name'];
        
        createDir('', $path);        
        move_uploaded_file($_FILES['file']['tmp_name'], $path . $_FILES['file']['name']);
      }
      else
      {
        $this->session->set_flashdata('error', 'Upload file size must be less than 6MB.');
        
        redirect(base_url() . 'post/addPost/' . $store_id);
      }
    }
    else
    {
      $data['filename'] = $this->session->flashdata('csv_filename');
    }
    
    if(!trim($data['filename']))
    {
      redirect(base_url().'post/addPost/'.$store_id);
    }
    
    $handle = fopen($path . $data['filename'], "r");

    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) 
    {
      $data['csv'][] = $row;
    }
    $data["association"] = "";
    fclose($handle);
    if(trim($this->input->post("association")))
    {
      $data["association"] = $this->input->post("association");
    }
    $this->load->view('store/format_csv', $data);
  }
  
  /**
   * function saveCsvData
   *
   * @param <int> $store_id     store id
   * 
   */
  function saveCsvData($store_id, $type = 0)
  {
    $user_role = Permissions::can_edit($store_id, $this->uid);
    
    if($user_role == 'view')
      redirect('moderate/index/'.$store_id);
    
    $filename = $this->input->post('filename');
    $cols_count = $this->input->post('cols_count');
    
    $path = $this->config->item('csv_upload_path');
    
    for ($i = 0; $i < $cols_count; $i++)
    {
      $name = 'hid_' . $i;
      if (isset($_POST[$name]))
      {
        $csv_config[$i] = $_POST[$name];
      }
    }    
   
    // save uploaded csv
    require_once(APPPATH.'libraries/products_csv.php');
    $product_csv = new Products_csv($store_id, $this->uid);    
    $product_csv->store_configuration();
    $product_csv->process_row($csv_config, 0);
    
    if(!$product_csv->has_error())
    {
      $file = fopen($path . $filename, 'r');
      $first = true;

      while ($row = fgetcsv($file, 1000, ","))
      {
        if($first)
        {
          $first = false;
          continue;
        }
        
        $product_csv->process_row($row, 1);
      }

      fclose($file);
    
      $this->session->set_userdata('products', $product_csv->get_products());
      
//      if($user_role != 'creator')
//      {
//        
//        redirect('post/linkProducts/' . $store_id);
//      }
      
      redirect('post/mapProducts/' . $store_id);
    }

    $this->session->set_flashdata('error', $product_csv->get_error());
    $this->session->set_flashdata('csv_filename', $filename);

    redirect('post/formatPost/' . $store_id);
  }
  
  /**
   * function postStyle
   *
   */
  function postStyle($id, $type='add')
  {
    $user_role = Permissions::can_edit($id, $this->uid);

    if($user_role == 'view')
      redirect('post/showProduct/'.$id);
    
    $this->store_id = $id;
    $error = array();
    $file_path = '';

    $this->layout = 'new_layout';
    $store_data = $this->store->getStoreById($id);

    $this->store_slot = array(
      'sub_heading' => 'Step 3: Customize Widget',
      'store'       => $store_data[0],
      'sub_links'   =>  get_sub_links('customize_widget'),
      'selected'    => 'customize_widget'
    );

    // default embed code
    $data = default_embed_code();

    // get member settings
    $teamId = $this->team->getTeamId($this->store_id);

    if ($teamId)
    {
      $member_settings = $this->team_member->getMemberSettings($this->uid, $teamId);
      $data = $member_settings['widget_settings']['appearance'];
      parse_embed_code($data);
    }

    // if post data
    if ($this->input->post('width'))
    {
      load_upload_library();

      if (trim($_FILES['store_logo']['name']))
      {
        $this->upload->file_name = make_image_file_name($_FILES['store_logo']['name']);

        // upload logo image
        if (!$this->upload->do_upload('store_logo'))
        {
          $error = array('error' => $this->upload->display_errors());
        }
        else
        {
          $file_path = $this->upload->file_name;

          // delete old logo
          if (isset($data['icon_path']) && trim($data['icon_path']) && $data['icon_path'] != default_logo_image())
          {
            $old_file_path = $this->config->item('root_dir') . '/uploads/' . $data['icon_path'];

            if (file_exists($old_file_path))
            {
              unlink($old_file_path);
            }

            $old_file_thumb = str_replace('uploads/', 'uploads/t-', $old_file_path);

            if (file_exists($old_file_thumb))
            {
              unlink($old_file_thumb);
            }
          }

          // resize image
          $this->load->helper('image');
          resize_image('uploads/' . $file_path, 'uploads/t-' . $this->upload->file_name);
        }
      }
      else
      {
        $file_path = $data['icon_path'];
      }

      // save data
      $data = array(
        'width' => $this->input->post('width'),
        'height' => $this->input->post('height'),
        'font_family' => $this->input->post('font_family'),
        'font_color' => $this->input->post('font_color'),
        'link_color' => $this->input->post('link_color'),
        'icon_path' => $file_path
      );

      if (empty($error))
      {
        $data = json_encode($data);
        $uid = $this->session->userdata('uid');

        //team_id
        $team_id = $this->team->getTeamId($id);
        //$this->team_member->updateStoreStyle($data, $uid, $team_id);

        if ($type == 'add')
        {
          redirect('dashboard');
        }

        redirect('dashboard');
      }
    }

    $data['type'] = $type;
    $data['error'] = $error;

    $this->load->view('postStyle', $data);
  }

  /**

   *

   * @param <type> $id

   * deleter posted products

   */
  function deletePost($id,$type) {
    
      $this->post->deletePost($id);      
      die();      

  }

  function updatePost($id)
  {
    $data = $this->post->getPostById($id);
    echo json_encode($data);
    die();
  }

  /**

   * Deleted stores

   *

   */
  function deleteStore($id) {
//    $this->team_member->deleteTeamMembers($id);
    $teams = $this->team->getByStoreId($id);
    print_r($teams);
    for($i=0; $i< count($teams);$i++)
      $this->team->deleteTeam($teams[0]['qa_team_id']);
    $this->store->deleteStore($id);    
    die();
//    redirect('post/stores');
  }

  /**

   * function embedCode

   *

   */
  function embedCode($id = 0, $type = 'store', $sub_id = '')
  {
    Permissions::can_edit($id, $this->uid);

    $upc_code = '';
    if ($type == 'product')
    {
      $sub_id = $this->product->getById($id, $sub_id);
    }

    $store = $this->store->getStoreById($id);
    
    $this->store_slot = array(
      'store' => $store[0],
      'sub_links'       =>  get_sub_links('settings'),
      'selected'        =>  'settings',
      'inner_links'     =>  get_inner_links_array('settings'),
      'inner_selected'  =>  'Catalog'
    );

    $data['store'] = $store[0];
    $data['sub_id'] = $sub_id;
    $data['embed_code'] = make_embed_code($data['store'], $sub_id, $type, $this->current_store_member_id);
    $data['encoded_code'] = make_embed_code($data['store'], $sub_id, $type, $this->current_store_member_id, true);

    $this->load->view('embedCode', $data);
  }

 /**
  * function showProduct
  *
  */
  function showProduct($id ='', $type='', $offset=0)
  {
    $this->layout = 'default';
    
    $data_view['user_role'] = Permissions::can_edit($id, $this->uid);

    $this->store_id = $id;
    $this->offset = $offset;

    $data_view['store'] = $this->store->getStoreById($id);
    $data_view['permission'] = store_permissions_mapping($data_view['store'][0]->qa_permission);

    // Get Product data to populate
    if (!trim($type) || $type == 'ajaxProduct')
    {
      $data = $this->product->getProduct($id, $offset, 10);
      $this->count_product = $this->product->getProductCount($id);
      $data_view['products'] = $data;

      $url = base_url() . 'post/showProduct/' . $this->store_id . '/ajaxProduct';
      $data_view['products_pager'] = $this->pager->get_pagination($this->count_product, $this->offset, 10, 'getMoreProducts', $url);

      if ($type == 'ajaxProduct')
      {
        $this->layout = 'empty';
        echo $this->load->view('product/productPagination', $data_view, true);
        exit;
      }
    }

    // Get Brand data to populate
    if (!trim($type) || $type == 'ajaxBrand')
    {
      $brand_data = $this->brand->getBrand($id, $offset, 10);
      $this->count_brand = $this->brand->getBrandCount($id);
      $config['total_rows'] = $this->count_brand;
      $data_view['brands'] = $brand_data;

      $url = base_url() . 'post/showProduct/' . $this->store_id . '/ajaxBrand';
      $data_view['brand_pager'] = $this->pager->get_pagination($this->count_brand, $this->offset, 10, 'getMoreBrands', $url);

      if ($type == 'ajaxBrand')
      {
        $this->layout = 'empty';
        echo $this->load->view('product/brandPagination', $data_view, true);
        exit;
      }
    }

    // Get Category data to populate
    if (!trim($type) || $type == 'ajaxCategory')
    {
      $data_category = $this->category->getCategory($id, $offset, 10);
      $this->count_category = $this->category->getCategoryCount($id);
      $data_view['category'] = $data_category;

      $url = base_url() . 'post/showProduct/' . $this->store_id . '/ajaxCategory';
      $data_view['category_pager'] = $this->pager->get_pagination($this->count_category, $this->offset, 10, 'getMoreCategories', $url);

      if ($type == 'ajaxCategory')
      {
        $this->layout = 'empty';
        echo $this->load->view("product/categoryPagination", $data_view, true);
        exit;
      }
    }

    $this->grpData = $this->group->getGroups($this->uid);
    
    $this->load->view('product/showProducts', $data_view);
  }

 /**
  * @param <type> $id
  * add a post <int> id and type = question or answer
  */
  function addQuestion($id, $type ='', $ref_type='', $ref_id='', $parent_id = '',$post_id= 0,$edit = 0)
  {
    $this->layout = 'empty';
    
    $this->store_id = $id;
    $this->imaageOptions = $this->store->checkImagePost($id);
    $this->type = $ref_type;
    $this->ref = $ref_id;
    $this->error = null;
    $this->parent_id = $parent_id;

    if (trim($this->input->post('title')) && trim($this->input->post('description')))
    {
      $data = array(
        'qa_ref_id'       => $ref_id,
        'qa_post_type'    => $ref_type,
        'qa_user_id'      => $this->session->userdata('uid'),
        'qa_title'        => $this->input->post('title'),
        'qa_description'  => $this->input->post('description'),
        'mod_level'       => 2,
        'mod_status'      => 'valid',
        'qa_created_at'   => gmdate('Y-m-d H:i:s'),
        'video_url'       => $this->input->post('video_url'),
        'video_caption'   => $this->input->post('video_caption'),
        'qa_store_id'     => $id
      );

      if ($type == 'answer')
      {
        $data['qa_parent_id'] = $parent_id;
      }

      if (isset($_FILES['image']['name']) && trim($_FILES['image']['name']))
      {
        $basePath = $this->config->item('root_dir') . '/uploads/stores/';
        $upload_path = createDir($id, $basePath);

        load_upload_library($upload_path);

        $this->upload->file_name = make_image_file_name($_FILES['image']['name']);

        // upload logo image
        if (!$this->upload->do_upload('image'))
        {
          $this->error = array('error' => $this->upload->display_errors());
        }
        
        if (!$this->error)
        {
          $data['image_url'] = $this->upload->file_name;

          $this->load->helper('image');
          resize_image($basePath . $id . '/' . $data['image_url'], $basePath . $id . '/t-' . $data['image_url'], 100, 100);
        }
        else
        {
          $this->error = 'Please upload a valid Image';
        }
      }

      if (!$this->error)
      {
        if (checkSpams($this->input->post('title'), $this->input->post('description')) != false)
        {
          if($edit == 0)
          {
            $new_post_id = $this->post->addPost($data);
          }
          else if($edit == 1)
          {
            $this->post->updatePost($post_id,$data);
          }

          if ($this->input->post('products') && count($this->input->post('products')) > 0)
          {            
            $this->linked_products->deleteProducts(isset($new_post_id)?$new_post_id:$post_id);
            
            $this->linked_products->save_products(isset($new_post_id)?$new_post_id:$post_id, $this->input->post('products'));            
          }

          echo '<script type="text/javascript">parent.remove_frame("frame_' . $ref_type . '_' . $ref_id . '")</script>';
          exit;
        }
        else
        {
          $this->error = 'Bad language used';
        }
      }

      if ($type == 'question')
      {
        $this->error = 'Please do not use abusive language';
        $this->load->view('product/question');
      }
      else
      {
        $this->error = 'Please do not use abusive language';
        $this->post_id = $parent_id;
        $this->load->view('product/answer');
      }
    }
    else if ($type == 'question')
    {
      if($post_id == 0)
        $this->load->view('product/question');
      else
      {
        $this->post_id = $post_id;
        $data  = $this->post->getPostById($post_id);
        $this->products = $this->linked_products->get_linked_by_post_id($post_id);        
        $this->product_count =  count($this->products);
        $data['post'] = $data[0];
        $this->load->view('product/question',$data);
      }
    }
    else
    {      
      if($post_id == 0)
      {        
        $this->post_id = $parent_id;
        
        $this->load->view('product/answer');

      }
      else
      {       
        $this->post_id = $post_id;
        $data  = $this->post->getPostById($post_id);
        $this->products = $this->linked_products->get_linked_by_post_id($post_id);
        $this->product_count =  count($this->products);
        $data  = $this->post->getPostById($post_id);
        $data['post'] = $data[0];        
        $this->load->view('product/answer',$data);
      }
    }
  }

  /**
   *
   * @param <type> $id

   * @param <type> $ref_id

   * @param <type> $ref_type

   * display post as question

   */
  function displayQuestion($id, $ref_id, $ref_type, $offset = 0)
  {
    $store = $this->store->getStoreById($id);
    $data_pager['permission'] = store_permissions_mapping($store[0]->qa_permission);
    $data_pager['role'] = Permissions::can_edit($id, $this->uid);
    $data_pager['results'] = $this->post->getPost($ref_id, $ref_type, 0, $offset, 10);

    if ($data_pager['results']) {
      $this->linked_products->get_linked($data_pager['results']);
    }

    $data_pager['count'] = $this->post->postDetailsCount($ref_id, $ref_type);

    $url = '"' . $ref_id . '", "' . $ref_type . '", this';
    $data_pager['pager'] = $this->pager->get_pagination($data_pager['count'], $offset, 10, 'viewQuestion', $url, false);

    echo json_encode(array($data_pager));

    die();
  }

  /**

   * @param <type> $id

   * @param <type> $ref_id

   * @param <type> $ref_type

   * @param <type> $post_id

   * dispaly post as anwser

   */
  function displayAnswer($id, $ref_type, $ref_id, $post_id, $offset = 0) {

    $data = $this->post->getPost($ref_id, $ref_type, $post_id, $offset);

    if ($data) {
      $this->linked_products->get_linked($data);
    }

    $count = $this->post->getAnswersCount($post_id);

    $url = 'this, "' . $ref_type . '", ' . $ref_id . ', '.$post_id.' ';

    $this->pager->create($count, $offset, 10, 'viewAnswer', $url, false);

    $data_pager = array(
        'pager' => $this->pager->anchors,
        'count' => $count,
        'results' => $data
    );
    $data_pager['role'] = Permissions::can_edit($id, $this->uid);
    echo json_encode(array($data_pager));

    die();
  }

  /**

   * Make groups of the products

   */
  function makeGroup() {

    $data = array(
        "qa_user_id" => $this->session->userdata('uid'),
        "qa_name" => $this->input->post('group_name')
    );

    $products = $this->input->post('products');



    if (is_numeric($this->input->post('group_id'))) {

      $id = $this->input->post('group_id');
    } elseif (trim($this->input->post('group_name')) != '') {

      $id = $this->group->addGroup($data);
    }
    print_r($products);
    if ($id > 0) {
      foreach ($products as $product) {

        $data = array(
            "qa_group_id" => $id,
            "qa_product_id" => $product
        );

        $this->product_group->addProductGroup($data);
      }
    }
    echo $id;

    exit;
  }

  /**

   * @param <type> $id

   * Map products of same title and diffrens product_id

   */
  function mapProducts($id)
  {
    if (trim($this->input->post('s_id')) != '' && trim($this->input->post('des_id')) != '')
    {
      $data = $this->product->getProductById($id, $this->input->post('des_id'));

      $data_update = array(
        'qa_product_title'       => $data['qa_product_title'],
        'qa_product_description' => $data['qa_product_description'],
        'product_image'          => $data['product_image'],
        'product_url'            => $data['product_url'],
        'qa_category_id'         => $data['qa_category_id'],
        'qa_brand_id'            => $data['qa_brand_id']
      );      
      $this->product->updateProduct($this->input->post('s_id'), $data_update);

      $this->product->deleteProductById($this->input->post('des_id'));

      die();
    }
    else
    {
      $products = $this->session->userdata('products');

      if (is_array($products) && count($products) > 0)
      {
        $data_view['views'] = $products;
        $data_view['user_role'] = Permissions::can_edit($id, $this->uid);
        $this->store_id = $id;
        $this->load->view('product/mapProducts', $data_view);
      }
      else
      {
        $this->session->set_userdata('products', null);
        redirect('moderate/index/' . $id);
      }
    }
  }

  function getPostByCategory($id,$offset = 0)
  {
    $this->layout = 'empty';
    $url= base_url().'post/getPostByCategory/'.$id;
    $this->id = $id;
    $this->offset = $offset;

    $data_pager['user_role'] = Permissions::can_edit($id, $this->uid);

    $store = $this->store->getStoreById($id);
    $data_pager['permission'] = store_permissions_mapping($store[0]->qa_permission);

    if (trim($this->input->post('cat_id')))
    {
      $data_pager['products'] = $this->product->getProductByCategoryId($this->input->post('cat_id'),$offset,10);
      $this->count_product = $this->product->getCountProductByCategoryId($this->input->post('cat_id'));
      $data_pager['products_pager'] = $this->pager->get_pagination($this->count_product, $offset,10, 'get_product_by_catagory',$url);
      echo $this->load->view('product/productPagination',$data_pager,true);
    }
    elseif (trim($this->input->post('brand_id')))
    {
      $data_pager['products'] = $this->product->getProductByBrandId($this->input->post('brand_id'),$offset,10);
      $this->count_product = $this->product->getCountProductByBrandId($this->input->post('brand_id'));
      $data_pager['products_pager'] = $this->pager->get_pagination($this->count_product, $offset,10, 'get_product_by_catagory',$url);

      echo $this->load->view('product/productPagination', $data_pager, true);
    }

    die();
  }

  function deleteGrp($id) {

    $this->group->deleteGroupByID($id);

    die();
  }

  function getProducts($id) {

    $data = $this->product_group->getGroupProducts($id);

    echo json_encode($data);

    die();
  }

  function getGroups() {

    $grpData = $this->group->getGroups($this->session->userdata('uid'));

    echo json_encode($grpData);

    die();
  }

  function testIt()
  {
    $this->load->helper('design');

    $rows = $this->db->query('SELECT * FROM qa_category LIMIT 10')->result_array();
    //print_r($rows);

    echo list_records_table($rows, array(array(
        'text'     => 'qa_category_id',
        'heading'  => 'Category Id'
      ),array(
        'text'     => 'qa_category_name',
        'heading'  => 'Category Title'
      ),array(
        'callback' => "get_product_by_catagory({id}, this, 'category')",
        'class'    => 'cat_product',
        'text'     => 'Products',
        'heading'  => 'View Products'
      )
    ));

    exit;
  }

  function testComponent()
  {
    $this->load->view('components/search');
  }
  
  function editCategory($store_id,$category_id,$type)
  {
    $data = array();
    if($type == "category")
    {
      $data = $this->category->getCategoryById($store_id,$category_id);
    }
    else if($type == 'brand')
    {
      $data = $this->brand->getBrandById($store_id,$category_id);
    }
    echo json_encode($data);
    die();
  }
  function updateCategoryBrand($type,$id)
  {
    if($type == 'category')
    {
      $data = array(
        'qa_category_name' => $this->input->post('name'),
        'qa_category_id' => $this->input->post('id'),
      );
      $this->category->updateCategoryById($id,$data);
      die();
    }
    else if($type == 'brand')
    {
      $data = array(
        'qa_brand_name' => $this->input->post('name'),
        'qa_brand_id' => $this->input->post('id'),
      );
      $this->brand->updateBrandById($id,$data);
      die();
    }
  }
  function editProduct($store_id,$product_id,$type = '')
  {
    $this->store_id= $store_id;
    $this->product_id = $product_id;
    $this->layout = 'empty';
    if($type == '')
    {   
      $data['product'] = $this->product->getProductById($store_id,$product_id);
      $this->load->view('product/editProduct',$data);
    }
    else if($type == 'submit')
    {      
      if(trim($this->input->post('name'))!='' && trim($this->input->post('product_id'))!='')
      {
        if(trim($this->input->post('product_pic'))!='')
        {          
          $save_images_locally = $this->store->imageOption($this->store_id);
          $name = save_product_image($this->input->post('product_pic'), $save_images_locally,$store_id);
        }
        $data_update = array(
            'qa_product_title'       => $this->input->post('name'),
            'qa_product_id'          => $this->input->post('product_id'),
            'qa_product_description' => $this->input->post('description'),
            'product_image'          => $this->input->post('product_pic')
        );
        
        $this->product->updateProduct($product_id,$data_update);
        echo '<script type="text/javascript">parent.remove_frame("frame_'.$product_id.'")</script>';
      }
    }
  }

  function deleteCategory($store_id,$id,$type)
  {
    if($type == 'category')
    {
      $this->category->deleteCategoryById($store_id,$id);
      $this->category->deleteCategoryProducts($store_id,$id);
      $this->post->deletePostByCategoryId($id,$type);
    }
    else if($type == 'brand')
    {
      $this->brand->deleteBrandById($store_id,$id);
      $this->category->deleteCategoryProducts($store_id,$id,$type);
      $this->post->deletePostByCategoryId($id,$type);
    }
    else if($type == 'product')
    {
      $this->product->deleteProductById($id);
      $this->product->deletePostByProductId($id);
    }
    die();
  }
  
  /**
   * function linkProducts
   * 
   * @param <int> $store_id   
   * 
   * it executes after upload catalog, if current user is not creator of the store
   * 
   */
  function linkProducts($store_id)
  {
    $user_role = Permissions::can_edit($store_id, $this->uid);
    $association =  "";
    if($user_role == 'view')
      redirect('moderate/index/'.$store_id);    

    
    if(trim($this->input->post("association")))
    {
      $data["association"] =  $this->input->post("association");
      $temp = $this->input->post("association");
      $filename = $this->input->post('filename');
      $cols_count = $this->input->post('cols_count');     
      $check = 0;
      for ($i = 0; $i < $cols_count; $i++)
      {
        $name = 'hid_' . $i;
        if (isset($_POST[$name]))
        {
          $csv_config[$i] = $_POST[$name];
          if($_POST[$name] == $this->input->post("association"))
            $check = i;
        }
      }
      require_once(APPPATH.'libraries/products_csv.php');
      $product_csv = new Products_csv($store_id, $this->uid);
      $product_csv->store_configuration();
      $path = $this->config->item('csv_upload_path');
      $data['products'] = array();
      $csv_data = array();
      $previous= "";
      $arr[][][] = null;
      if(!$product_csv->has_error())
      {
          $file = fopen($path . $filename, 'r');
          $first = true;

          while ($row = fgetcsv($file, 1000, ","))
          {            
            if($first)
            {
              $first = false;
              continue;
            }                        
            if(strpos($temp,"category") == true || strpos($temp,"brand") == true || strpos($temp,"product") == true)
            {
               ($previous == $row[$check])? $i++ : $i=0;
               for ($k = 0; $k < $cols_count; $k++)
                {
                  if(isset($row[$k]) && trim($row[$k]) && isset($csv_config[$k]))
                     $arr[$row[$check]][$i][$csv_config[$k]] = $row[$k];                  
                }                
                $db_data []= $row[$check];
                $previous = $row[$check];
            }           
          }          
          $tok = explode("_", $temp);
          $func_name = "check_".$tok[1]."_map";
          $id_data [] = null;
          if($tok[1] != "product")
          {            
            $results = $this->$tok[1]->$func_name($temp,$db_data,$store_id);
            if(count($results) > 0)
            {
              for($i = 0; $i< count($results);$i++)
              {
                $results[$i] = $results[$i]["id"];
              }
              $id_data [] = $results;
              $results = $this->product->check_product_map($temp, $results,$store_id,$this->uid);
            }
          }
          else
          {
            $results = $this->product->check_product_map($temp, $db_data,$store_id,$this->uid);            
          }          
          foreach ($id_data as $key => $value)
          {
            for($k = 0; $k < count($value);$k++)
            {              
              for($i = 0; $i <count($results);$i++)
              {                
                if($value[$k] == $results[$i][$temp])
                {
                  $data['products'] []= $arr[$db_data[$i]];
                }
              }
            }
          }          
          fclose($file);
      }
      $data["file_name"] = $filename;
      $data["columns"] = $csv_config;
      $data["count"] = $cols_count;

      }
      $data['store_id'] = $store_id;
//    $data['products'] = $this->product->getProductsForMapping($store_id, $this->uid);


    $this->load->view('product/linkProducts', $data);
}
  
  /**
   * function productList
   * 
   * @param <int> $store_id   
   * 
   */
  function productsList($store_id)
  {
    $this->no_layout = true;
    
    $user_role = Permissions::can_edit($store_id, $this->uid);
    
    if($user_role == 'view')
    {
      exit;
    }
    $data["association"] = "";
     if(trim($this->input->post("association")))
      $data["association"] =  $this->input->post("association");
    
    $store = $this->store->getStoreById($store_id);
    $data['products'] = $this->product->getProductsForMapping($store_id, $store[0]->qa_user_id, false);    
    
    $this->load->view('product/productsList', $data);
  }
  
  /**
   * function saveProductMapping
   * 
   * @param <int> $store_id
   * @param <int> $product_id
   * @param <int> $map_id
   * 
   * 
   */
  function saveProductMapping()
  {
    $store_id = $this->input->post('store_id');
    echo $map_id = $this->input->post('map_id'); 
    echo $product_id = $this->input->post('p_id');
    
    $user_role = Permissions::can_edit($store_id, $this->uid);
    
    if($user_role == 'view')
    {
      echo 0;
      exit;
    }    
    if(trim($this->input->post("associate")))
    {
      $cols_count = $this->input->post("count");
      for ($i = 0; $i < $cols_count; $i++)
      {
        $name = 'hid_' . $i;
        if (isset($_POST[$name]))
        {
          $csv_config[$_POST[$name]] = $_POST[$name];
        }
      }      
      require_once(APPPATH.'libraries/products_csv.php');
      $product_csv = new Products_csv($store_id, $this->uid,"",true);
      $product_csv->store_configuration();
      $product_csv->process_row($csv_config, 0);      
      $save_product = array(
      'store id' => $store_id,
      'user id'     => $this->uid,
      'product id' => $this->input->post("p_id"),
      'title' => $this->input->post("product_title"),
      'description' => $this->input->post("product_description"),
      'brand id' => $this->input->post("product_description"),
      'brand name' => $this->input->post("brand name"),
      'category id' => $this->input->post("category_id"),
      'category name' => $this->input->post("category_name"),
      'image url' => $this->input->post("image_url"),
      'product url' => $this->input->post("product_url"),
      'parent id' => $this->input->post("parent_id"),
      'created at'  => date('Y-m-d H:i:s')
    );
     
      $product_id = $product_csv->process_row($save_product, 1);
      echo "product id :".$product_id;
    }    
    $this->product->saveProductMapping($product_id, $map_id);
    echo $this->db->last_query();
    exit;
  }

  function webInfo($store_id = "",$type ="")
  {
    $store_data = $this->store->getStoreById($store_id);
    Permissions::can_edit($store_data[0]->qa_store_id, $this->uid);
    $this->store_slot = array(
      'sub_heading'    =>  'Web Settings',
      'store'          =>  $store_data[0],
      'sub_links'      =>  get_sub_links('settings'),
      'inner_links'    =>  get_inner_links_array('store_edit'),
      'selected'       =>  'settings',
      'inner_selected' =>  'Web Settings'
    );
    
    $team_settings = $this->session->userdata('team_settings');
    
    if($store_id == "" )
      redirect(base_url());
    $this->layout = 'new_layout';
    $this->team_id = $team_settings['qa_team_id'];
    $this->team_member_id= $team_settings['qa_team_member_id'];
    $this->store_id = $store_id;
    if(trim($this->input->post("domainName"))&& trim($this->input->post("loginUrl")) && trim($this->input->post("redirectParam")))
    {
      $data = array(
          'qa_site_name'  => $this->input->post("domainName"),
          'qa_login_url'  => $this->input->post("loginUrl"),
          'qa_thanks_url' => $this->input->post("redirectParam")
      );      
      $this->team_member->updateTeamMember($data , $team_settings['qa_team_member_id'],$team_settings['qa_team_id']);
      if($type != "edit")
        redirect('post/addPost/'.$store_id);
    }    
    if($type == "edit")
    {      
      $data_edit["web_info"] = $this->team_member->getWebInfo($team_settings['qa_team_id'], $team_settings['qa_team_member_id'], $store_id);      
      $this->load->view("webInfo", $data_edit);
    }
    else
    {
      $this->load->view("webInfo");
    }
  }
  
}