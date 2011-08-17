<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of moderator
 *
 * @author purelogics
 */
class moderator extends qaController
{
  function  moderator()
  {
    parent::__construct();
    
    $this->load->model('qa_product','product');
    $this->load->model('qa_brand','brand');
    $this->load->model('post_spam','spam');
    $this->load->model('qa_catagory','category');
    //$this->load->model('post_moderation','moderation');
    $this->load->model('qa_team_members',"qa_team_members");
    $this->load->model('email_queue',"email");
    $this->load->model('qa_login',"login");

    $this->mod_level = ($this->is_admin) ? 0 : 1;
    $this->mod_status = ($this->is_admin) ? 'IS NULL' : 'valid';
  }
  
  function index($id= 0,$offset=0)
  {
    $this->moderation_type = 0;

    $this->store_id = $id;
    
    if($id !=0)
    {
      Permissions::can_edit($id, $this->uid);

      $this->store_info = $this->store->getStoreById($this->store_id);

      $this->store_slot = array(
        'sub_heading' => 'Moderate',
        'store'       => $this->store_info[0],
        'sub_links'   => get_sub_links('settings'),
        'selected'    => 'moderate'
      );
    }
    
    $this->offset = $offset;
    $this->uid = $this->session->userdata('uid');

    $data_view = array();
    $data_view['views'] = array();
    $data_view['category']= array();
    $data_view['brands']= array();
    $data_view = array_merge($data_view, parse_grid_params());

    $this->count_brand = 0;
    $this->count_category = 0;
    $this->count_product = 0;
    
    if(isset($this->store_info[0])&&  $this->store_info && $this->store_info[0]->qa_store_id)
    {
      //get Products
      $query_params = moderate_params($this->store_info[0]->moderation_type);
      
        $this->moderation_type = $this->store_info[0]->moderation_type;
    }    
    $this->load->view('moderate/moderate_index', $data_view);
  }

  /**
   * @param <type> $id
   * @param <type> $ref_id
   * @param <type> $ref_type
   * display post as question
   */
  function displayQuestion($id, $ref_id, $ref_type)
  {
    $additional = array();
    
    $role = Permissions::can_edit($id, $this->uid);
    
    $columns = array('qa_post_id', 'qa_title', 'qa_description');
    
    if($role!= "view")
    {
     $additional = array(
      '<select name = "moderate">
       <option value = "spam"> Spam</option>
       <option value = "in_valid" >Invalid</option>
       <option value = "irrelevant" >Irrelevant </option>
       <option value = "valid" >Valid</option>
       <option value = "abusive" >Abusive </option>
       </select><a  href="javascript:;" rel = "{qa_post_id}" class="update">Update</a>
       <input type ="hidden" name = "type" id ="type" value = "question"/>'
      );
    }
    
    $data = array();
    $this->store_id = $id;
    $count = 0;
    
    $this->store_info = $this->store->getStoreById($this->store_id);
    
    $params = parse_grid_params();
    
    if($this->store_info && $this->store_info[0]->qa_store_id)
    {
      $store_mod_type = $this->store_info[0]->moderation_type;
      if(in_array($store_mod_type, array(3, 4)))
      {
        $this->mod_level = -1;
        $this->mod_status = 'IS NULL';
      }
      
      $data = $this->post->getPost($ref_id, $ref_type, 0, $params['grid_offset'] , $params['grid_limit'], $this->mod_level, $this->mod_status);
      
      $count = $this->post->getPaginatePostCount($ref_id, $ref_type, 0, $this->mod_level, $this->mod_status);

    }
    
    $data = format_grid_data($data, $count, $params['grid_page'], $params['grid_limit'], 'qa_post_id', $additional, $columns,true);
    
    render_json_response($data);
    die();
  }

  /**   *
   * @param <type> $id
   * @param <type> $ref_id
   * @param <type> $ref_type
   * @param <type> $post_id
   * dispaly post as anwser
   */
  function displayAnswer($id, $ref_id, $ref_type, $post_id = -1)
  {
    $data = array();
    $this->store_id = $id;
    
    $role = Permissions::can_edit($id, $this->uid);
    
    $this->store_info = $this->store->getStoreById($this->store_id);
    $params = parse_grid_params();
    
    $columns = array('qa_post_id', 'qa_title', 'qa_description');
    
    if($role!= "view")
    {
     $additional = array(
      '<select name = "moderate">
       <option value = "spam">Spam</option>
       <option value = "in_valid" >Invalid</option>
       <option value = "valid" >Valid</option>
       <option value = "irrelevant" >Irrelevant </option>
       <option value = "abusive" >Abusive </option>
       </select><a  href="javascript:;" rel = "{qa_post_id}" class="update">Update</a>
       <input type ="hidden" name = "type" id ="type" value = "answer"/>'
      );
    }
    else
    {
      $additional = array();
    }
    
    if($this->store_info && $this->store_info[0]->qa_store_id)
    {
      $store_mod_type = $this->store_info[0]->moderation_type;
      if(in_array($store_mod_type, array(3, 4)))
              
      {
        $this->mod_level = -1;
        $this->mod_status = 'IS NULL';
      }
      
      $data = $this->post->getPost($ref_id, $ref_type, $post_id, $params['grid_offset'] , $params['grid_limit'], $this->mod_level, $this->mod_status);      
      $count = $this->post->getPaginatePostCount($ref_id, $ref_type, $post_id, $this->mod_level, $this->mod_status);
      
      $data = format_grid_data($data, $count, $params['grid_page'], $params['grid_limit'], 'qa_post_id', $additional, $columns,true);
      
      render_json_response($data);
    }
    
    echo json_encode(array($data_veiw));
    die();
  }

  function updateStatus($store_id, $post_id, $mod_status, $mod_type = '')
  {
    $this->moderation_type = $this->store->getModerationSetting($store_id);

    $mod_level = 0;
    if($this->moderation_type == 5)
    {
      $mod_level = (!$this->is_admin) ? 2 : 1;
    }
    
    $data_moderate = array(
      'qa_post_id' => $post_id,
      'qa_user_id' => $this->uid,
      'mod_status' => $mod_status,
      'qa_created' => gmdate('Y:m:d:H:i:s')
    );

    if($mod_level > 0)
    {
      $data_moderate['mod_level'] = (!$this->is_admin) ? 2 : 1;
    }

    $this->moderation->addPostModeration($data_moderate);

    if($mod_status == 'valid' && !trim($mod_type))
    {
      $this->spam->deleteSpams($post_id);
    }
    else
    {
      $this->post->updateModeratoin($post_id, $mod_status, $mod_level);
      if($mod_level == 2)
      {
        $data = array(
            'ref_id'        => $post_id,
            'type'          => ($this->input->post("type") == "question")?'question_approved':'answer_approved',
            'created_at' => gmdate('Y-m-d H:i:s')
        );
        $this->login->updateAnswersCount($this->uid);
        $this->email->set_email_queue($data);
      }      
    }
    
    exit;
  }
  
  function spamPosts($id = 0, $type = '')
  {
    $this->store_id = $id;

    $data_view['answers'] = array();
    $data_view['questions'] = array();
    $data_view['brand_questions'] = array();
    $data_view['brand_answers'] = array();
    $data_view['product_questions'] = array();
    $data_view['product_answers'] = array();
    $data_view['count_category'] = 0;

    //geting questions of category brand and product
    if($this->store_id > 0)
    {
      $this->role = Permissions::can_edit($id, $this->uid);

      $data_view['questions'] = $this->post->getSpamPostCategory($id);
      $data_view['count_category'] = $this->post->getSpamPostCategoryCount($id);
      if($type == 'ajaxCategory')
      {
        render_json_paginated_data($data['count_category'], $offset, 10, $data_view['questions'], 'populateQuestions', 'moderator/spamPosts/'.$this->store_id.'/ajaxCategory/1');
      }

      $data_view['brand_questions'] = $this->post->getSpamPostBrand($id);
      $data_view['product_questions'] = $this->post->getSpamPostProduct($id);

      //geting answers of category brand and product
      $data_view['answers'] = $this->post->getSpamPostCategory($id, 1);
      $data_view['brand_answers'] = $this->post->getSpamPostBrand($id, 1);
      $data_view['product_answers'] = $this->post->getSpamPostProduct($id, 1);
    }
    $data_view = array_merge($data_view, parse_grid_params());
    $this->load->view('moderate/spam', $data_view);
  }
  
  function showHistory($post_id,$offset = 0 )
  {
    $data  = $this->spam->getSpamHistory($post_id,$offset);    
    $params = parse_grid_params();
    $count = $this->spam->getSpamHistoryCount($post_id);
    $columns = array('user_id', 'description', 'created_at');
    $data = format_grid_data($data, $count, $params['grid_page'], $params['grid_limit'], 'user_id', '', $columns);
    render_json_response($data);
    die();
  }
  
  function brand_moderator($store_id, $type = 'moderate', $answer = 0)
  {
     $this->store_info = $this->store->getStoreById($store_id);
     $query_params = moderate_params($this->store_info[0]->moderation_type);
     $params = parse_grid_params();
     $additional = array(
      '<a rel="brand|{id}" href="javascript:;" class="viewAsnwer">View Answer</a>',
      '<a rel="brand|{id}" href="javascript:;" onclick = "viewQuestion({id}, \'brand\', this)" class="view-report">View Question</a>'
      );
      $columns = array('id', 'qa_brand_id', 'qa_brand_name');
     if($type == "spam")
     {
       $columns = array('post_id','id', 'qa_brand_id', 'qa_brand_name');
       $additional = array(
        '<select name = "moderate">
         <option value = "spam"> Spam</option>
         <option value = "in_valid" >Invalid</option>
         <option value = "valid" >Valid</option>
         <option value = "irrelevant" >Irrelevant </option>
         <option value = "abusive" >Abusive </option>
         </select><a  href="javascript:;" rel = "{post_id}" class="update">Update</a>',
        '<a  href="javascript:;" rel = "{post_id}" class="history">History</a>'
        );
      if($answer == 0)
      {
        //spammed questions for brands
        $brand_questions = $this->post->getSpamPostBrand($store_id);
        $count  = $this->post->getSpamPostBrandCount($store_id);
        $brand_questions = format_grid_data($brand_questions, $count, $params['grid_page'], $params['grid_limit'], 'post_id', $additional, $columns,true);
        render_json_response($brand_questions);
      }
      else
      {
        //spammed answers for brands
        $brands_answer= $this->post->getSpamPostBrand($store_id, 1);
        $count  = $this->post->getSpamPostBrandCount($store_id, 1);
        $brands_answer = format_grid_data($brands_answer, $count, $params['grid_page'], $params['grid_limit'], 'post_id', $additional, $columns,true);
        render_json_response($brands_answer);
      }      
     }
     $brands = $this->brand->getModerateBrand($store_id ,$params['grid_offset'] , $params['grid_limit'], $query_params,$params['grid_column'], $params['grid_order']);
     $count = $this->brand->getBrandCount($store_id);         

     $brands = format_grid_data($brands, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

    render_json_response($brands);

    exit;
  }

  function product_moderator($store_id,$type = 'moderate',$answer = 0)
  {
     $this->store_info = $this->store->getStoreById($store_id);
     $query_params = moderate_params($this->store_info[0]->moderation_type);
     $params = parse_grid_params();
     $additional = array(
      '<a rel="product|{id}" href="javascript:;" class="viewAsnwer">View Answers</a>',
      '<a rel="product|{id}" href="javascript:;" onclick = "viewQuestion({id}, \'product\', this)" class="view-report">View Questions</a>'
     );

     $columns = array('id', 'qa_product_id', 'qa_product_title');
     if($type == "spam")
     {
       $columns = array('post_id', 'id', 'qa_product_id', 'qa_product_title');
       $additional = array(
        '<select name = "moderate">
         <option value = "spam"> Spam</option>
         <option value = "in_valid" >Invalid</option>
         <option value = "valid" >Valid</option>
         <option value = "irrelevant" >Irrelevant </option>
         <option value = "abusive" >Abusive </option>
         </select><a  href="javascript:;" rel = "{post_id}" class="update">Update</a>',
        '<a  href="javascript:;" rel = "{post_id}"  class="history">History</a>'
        );
       if($answer == 0)
       {
         $product_questions = $this->post->getSpamPostProduct($store_id);         
         $count  = $this->post->getSpamPostProductCount($store_id);
         $product_questions = format_grid_data($product_questions, $count, $params['grid_page'], $params['grid_limit'], 'post_id', $additional, $columns, true);
         render_json_response($product_questions);
       }
       else
       {
        $product_answers = $this->post->getSpamPostProduct($store_id, 1);
        $count  = $this->post->getSpamPostProductCount($store_id, 1);
        $product_answers = format_grid_data($product_answers, $count, $params['grid_page'], $params['grid_limit'], 'product_id', $additional, $columns, true);
        render_json_response($product_answers);
       }
     }

     $products = $this->product->getModerateProduct($store_id ,$params['grid_offset'] , $params['grid_limit'], $query_params,$params['grid_column'], $params['grid_order']);     
     $count = $this->product->getProductCount($store_id);
     $products = format_grid_data($products, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

     render_json_response($products);

    exit;
  }

  function category_moderator($store_id, $type = 'moderate', $answer =0)
  {
     $this->store_info = $this->store->getStoreById($store_id);
     $query_params = moderate_params($this->store_info[0]->moderation_type);
     $additional = array(
      '<a rel="category|{id}" href="javascript:;" class="viewAsnwer">View Answers</a>',
      '<a rel="cateogry|{id}" href="javascript:;" onclick = "viewQuestion({id}, \'category\', this)" class="view-report">View Questions</a>'
      );
     $columns = array('id', 'qa_category_id', 'qa_category_name');
     $params = parse_grid_params();
     if($type == "spam")
     {
       $columns = array('post_id','id', 'qa_brand_id', 'qa_brand_name');
       $additional = array(
        '<select name = "moderate">
         <option value = "spam"> Spam</option>
         <option value = "in_valid" >Invalid</option>
         <option value = "valid" >Valid</option>
         <option value = "irrelevant" >Irrelevant </option>
         <option value = "abusive" >Abusive </option>
         </select><a  href="javascript:;" rel = "{post_id}" class="update">Update</a>',
        '<a  href="javascript:;" rel = "{post_id}"  class="history">History</a>'
        );
       if($answer == 0)
       {
         $category_questions = $this->post->getSpamPostCategory($store_id);
         $count = $this->post->getSpamPostCategoryCount($store_id);
         $category_questions = format_grid_data($category_questions, $count, $params['grid_page'], $params['grid_limit'], 'post_id', $additional, $columns,true);
         render_json_response($category_questions);
       }
       else
       {         
        $category_answers = $this->post->getSpamPostCategory($store_id, 1);
        $count = $this->post->getSpamPostCategoryCount($store_id, 1);
        $category_answers = format_grid_data($category_answers, $count, $params['grid_page'], $params['grid_limit'], 'post_id', $additional, $columns,true);
        render_json_response($category_answers);
       }
       exit;
     }
     
     $category = $this->category->getModerateCategory($store_id ,$params['grid_offset'] , $params['grid_limit'], $query_params,$params['grid_column'], $params['grid_order']);
     $count = $this->category->getCategoryCount($store_id);
     $category = format_grid_data($category, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);

    render_json_response($category);

    exit;
  }
}