<?php

/*
 * class Widget
 *
 * author - kashif
 */
class Widget extends Controller
{
  function Widget()
  {
    parent::Controller();
    error_reporting(false);

    // load models and libraries
    $this->load->model('posts','post');
    $this->load->model('stores', 'store_model');
    $this->load->model('qa_catagory', 'category');
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_product', 'product');
    $this->load->model('qa_team_members','team_member');
    $this->load->model('qa_login','users');
    $this->load->model('post_vote', 'post_vote');
    $this->load->model('post_products', 'linked_products');
    $this->load->model('widget_users', 'model_widget_user');

    require_once APPPATH . 'models/post_history.php';
    
    $this->load->library('qawidget');
    $this->load->library('widget_pager');
    
    $this->load->helper('widget');
    $this->load->helper('session');

    $this->uid = $this->session->userdata('uid');

    $this->layout = 'widget';
  }

  function main($team_member_id = '', $store_id = '', $ref_id = '', $ref_type = '', $widget_user_id = '', $navigate_to_post_id = 0)
  {
    $not_configured = array('Q&A Wiki is not configured properly');

    may_require_exit($not_configured, ($store_id && $ref_id && $ref_type && $team_member_id));
    
    // session management
    if($widget_user_id)
    {
      $widget_user_email = trim($_REQUEST['qce']);
      widget_session_by_referer($widget_user_id, $widget_user_email);
    }
    
    $data['session_key'] = instantiate_widget_session($team_member_id, $store_id, $ref_id, $ref_type);

    // item info
    $model_class = $this->qawidget->getModel($ref_type);
    may_require_exit($not_configured, $model_class);

    // get detail for a product, category or brand
    $data['info'] = $this->$model_class->getDetails($store_id, $ref_id);
    may_require_exit($not_configured, $data['info']);
    
    // mapped product
    if(!isset($data['info']['linked_id']))
      $data['info']['linked_id'] = 0;
    
    $prod_map_id = $data['info']['id'];
    if($data['info']['linked_id'] > 0)
    {
      $prod_map_id = $data['info']['linked_id'];
    }

    // set session data
    session_set_widget_data($data['session_key'], 'team_id', $data['info']['qa_team_id']);
    session_set_widget_data($data['session_key'], 'store_name', $data['info']['qa_store_name']);
    session_set_widget_data($data['session_key'], 'cont_threshold', $data['info']['qa_threshold']);
    session_set_widget_data($data['session_key'], 'prod_map_id', $prod_map_id);

    // member settings - used in widget for icon
    $data['widget'] = $this->team_member->getTeamMemberById($team_member_id);
    may_require_exit($not_configured, $data['widget']);

    $data['widget'] = $data['widget'][0];

    if($data['widget']->widget_settings)
    {
      $data['widget']->widget_settings = json_decode($data['widget']->widget_settings);
    }

    // questions
    $data['info']['qa_permission'] = store_permissions_mapping($data['info']['qa_permission']);

    // if no question exist and ref_type is not product
    $total_questions = 0;
    $offset = 0;
    
    if($ref_type == 'product')
    {
      if(trim($navigate_to_post_id))
      {
        $offset = $this->post->get_question_offset($navigate_to_post_id, $prod_map_id, $ref_type);
      }
      
      $data['questions'] = $this->post->postDetails(session_get_widget_user('user_id'), $prod_map_id, $ref_type, '', $offset);
    }
    else
    {
      if(trim($navigate_to_post_id))
      {
        $offset = $this->post->get_question_offset_for_category($navigate_to_post_id, $prod_map_id, $ref_type);
      }
      
      $data['questions'] = $this->post->getPostForProducts(session_get_widget_user('user_id'), $prod_map_id, $ref_type, '', $offset);
    }
    
    if($data['questions'])
    {
      $total_questions = $this->post->postDetailsCount($prod_map_id, $ref_type);
      if($ref_type != 'product')
      {
        $total_questions += $this->post->getPostForProductsCount($prod_map_id, $ref_type);
      }
    }
    
    format_post_time($data['questions']);

    $this->linked_products->get_linked($data['questions']);

    $this->team_member->get_badges($data['questions'], $data['info']['qa_team_id']);

    // pagination
    $data['pagination'] = $this->widget_pager->get_pagination($total_questions, $offset);
    
    $data['nick_name'] = session_get_widget_user('user_name');

    // short url
    generate_short_url('', 'widget_load', $store_id, $ref_id, $ref_type, session_get_widget_user('user_id'));

    // track widget user
    $this->model_widget_user->add_widget_user(session_get_widget_user('user_id'), $ref_id, $ref_type);
    
    if(isset($_REQUEST['debug']))
      print_r($this->db->queries);
    
    // return data
    render_json_response(array($data));
  }

  /**
   * function questions
   *
   * @param <string> $session_key
   * @param <string> $filters
   * @param <int> $offset
   *
   * return list of questions
   *
   */
  function questions($session_key, $filter, $offset = 0, $search_text = '')
  {
    $widget_data = get_complete_widget_session($session_key);

    $total_questions = 0;
    if($widget_data['ref_type'] == 'product')
    {
      $data['questions'] = $this->post->postDetails($widget_data['user_id'], $widget_data['prod_map_id'], $widget_data['ref_type'], $filter, $offset, 5, $search_text);
    }
    else
    {
      $data['questions'] = $this->post->getPostForProducts($widget_data['user_id'], $widget_data['prod_map_id'], $widget_data['ref_type'], $filter, $offset, 5, $search_text);
    }
    if($data['questions'])
    {
      $total_questions = $this->post->postDetailsCount($widget_data['prod_map_id'], $widget_data['ref_type'], $filter, 0, $search_text);
      if($widget_data['ref_type'] != 'product')
      {
        $total_questions += $this->post->getPostForProductsCount($widget_data['prod_map_id'], $widget_data['ref_type'], $filter, 0, $search_text);
      }
    }

    format_post_time($data['questions']);

    $this->linked_products->get_linked($data['questions']);

    $this->team_member->get_badges($data['questions'], $widget_data['team_id']);

    // pagination
    $data['pagination'] = $this->widget_pager->get_pagination($total_questions, $offset);

    // short url
    generate_short_url('', 'question', $widget_data['store_id'], $widget_data['ref_id'], $widget_data['ref_type'], $widget_data['user_id'], 'questions');

    render_json_response(array($data));
  }

  /**
   * function answers
   *
   * @param <string> $session_key
   * @param <int> $question_id
   * @param <int> $offset
   *
   * return list of answers
   *
   */
  function answers($session_key, $ref_id, $ref_type, $question_id, $offset = 0)
  {
    $widget_data = get_complete_widget_session($session_key);

    $answers = $this->post->getPost($ref_id, $ref_type, $question_id, $offset, 5, -2, 'valid', $widget_data['user_id']);

    format_post_time($answers);

    $this->post_vote->get_vote_count($answers);

    $this->linked_products->get_linked($answers);

    $this->team_member->get_badges($answers, $widget_data['team_id']);
    
    $widget_ids = array($widget_data['ref_id']);
    if(isset($widget_data['prod_map_id']) && trim($widget_data['prod_map_id']))
    {
      $widget_ids[] = $widget_data['prod_map_id'];
    }
    
    $this->model_widget_user->get_top_contributors($answers, $widget_ids, $widget_data['ref_type'], $widget_data['cont_threshold']);

    // short url
    generate_short_url('', 'answer', $widget_data['store_id'], $widget_data['ref_id'], $widget_data['ref_type'], $widget_data['user_id'], 'answers-'.$question_id);

    render_json_response($answers);
  }

  /**
   * function savePost
   *
   * @param <string> $session_key
   * @param <string> $type
   *
   * save question or answer
   *
   */
  function savePost($session_key, $type)
  {
    $this->load->model('email_queue');
    
    $response = 'failure';
    $this->redirect = false;
    $data = array();

    $widget_data = get_complete_widget_session($session_key);
    may_require_exit($response, $widget_data['user_id']);
    
    $question_id = isset($_REQUEST['qawiki_question_id']) ? $_REQUEST['qawiki_question_id'] : null;
    $type = strtolower(trim($type));
    
    // upload image
    if(in_array('qawiki_image', array_keys($_FILES)))
    {
      $this->redirect = $_SERVER['HTTP_REFERER'];
      
      $data['image_url'] = save_post_image($widget_data['store_id'], 'qawiki_image');
    }

    // make data
    if($type == 'question')
    {
      $question = new stdClass();
      $question->qa_ref_id = $widget_data['ref_id'];
      $question->qa_post_type = $widget_data['ref_type'];
      $question->qa_post_id = 0;
      $title = trim($_REQUEST['qawiki_question']);
    }
    else
    {
      $question = $this->post->getPostById($question_id);
      $question = $question[0];
      $title = trim($_REQUEST['qawiki_answer']);
    }
    
    if($question)
    {
      // get store info
      $store_info = $this->store_model->getStoreById($widget_data['store_id']);

      $data['qa_ref_id'] = $question->qa_ref_id;
      $data['qa_post_type'] = $question->qa_post_type;
      $data['qa_user_id'] = $widget_data['user_id'];
      $data['qa_title'] = $title;
      $data['qa_description'] = trim($_REQUEST['qawiki_description']);
      $data['qa_parent_id'] = $question->qa_post_id;
      $data['qa_created_at'] = gmdate('Y-m-d H:i:s');
      $data['qa_store_id'] = $widget_data['store_id'];
      
      if($store_info && in_array($store_info[0]->moderation_type, array(1, 2)))
      {
        if($store_info[0]->moderation_type == 2 && !checkSpams($data['qa_title'], $data['qa_description']))
        {
          render_json_response('spam');
        }
        $data['mod_status'] = 'valid';
        
        // update answers count
        $this->users->updateAnswersCount($widget_data['user_id']);
      }

      if(isset($_REQUEST['qawiki_video_url']))
      {
        $data['video_url'] = trim($_REQUEST['qawiki_video_url']);
        $data['video_caption'] = trim($_REQUEST['qawiki_video_caption']);
      }
      
      if(isset($_REQUEST['qawiki_email_opt']))
      {
        $data['email_opt_in'] = 1;
      }

      $this->db->insert('store_item_posts', $data);
      $post_id = $this->db->insert_id();

      // save related products
      if(isset($_REQUEST['qawiki_products']) && count($_REQUEST['qawiki_products']) > 0)
      {
        $products = $_REQUEST['qawiki_products'];

        $this->linked_products->save_products($post_id, $products);
      }
      
      // if type is question, send email to user whose question is answered
      if($type == 'answer' && $question->qa_user_id != $data['qa_user_id'] && $question->email_opt_in == 1)
      {
        $this->email_queue->save($post_id, 'answer', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''));
      }
      
      $this->email_queue->save($post_id, $type.'_approved', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''));

      $response = 'success';
    }

    if(!trim($widget_data['user_name']))
    {
      $nick_name = trim($_REQUEST['qawiki_nickname']);
      $email = isset($_REQUEST['qawiki_email']) ? $_REQUEST['qawiki_email'] : '';
      $this->users->update_widget_user($widget_data['user_id'], $nick_name, $email);

      session_set_widget_user('user_name', $nick_name);
    }

    // redirect if not ajax
    if(trim($this->redirect))
    {
      redirect($this->redirect);
    }
    
    render_json_response(array($response));
  }

  /**
   * function saveSpam
   *
   * @param <string> $session_key
   *
   * save spam
   *
   */
  function saveSpam($session_key)
  {
    $widget_data = get_complete_widget_session($session_key);
    may_require_exit('failure', $widget_data['user_id']);
    
    $data['post_id'] = trim($_REQUEST['qawiki_post_id']);
    $data['description'] = trim($_REQUEST['qawiki_issue']);
    $data['user_id'] = $widget_data['user_id'];
    $data['created_at'] = gmdate('Y-m-d H:i:s');

    $this->db->insert('post_spam', $data);

    render_json_response(array('success'));
  }

  /**
   * function saveVote
   *
   * @param <string> $session_key
   *
   * save vote
   *
   */
  function saveVote($session_key, $post_id, $type)
  {
    $widget_data = get_complete_widget_session($session_key);
    may_require_exit('failure', ($widget_data['user_id'] && trim($post_id)));

    $type = strtolower(trim($type));
    if($type == 'up')
      $data['pos_vote'] = 1;
    else
      $data['neg_vote'] = 1;

    $data['post_id'] = trim($post_id);
    $data['user_id'] = $widget_data['user_id'];
    $data['created_at'] = gmdate('Y-m-d H:i:s');

    if(!$this->post_vote->has_voted($data['user_id'], $data['post_id']))
    {
      $vote_id = $this->post_vote->save_vote($data);
      
      // update thumbs count
      $user_id = $this->post->isAnswerFromWidgetUser($data['post_id']);
      if($user_id && $user_id != $data['user_id'])
      {
        $this->users->updateThumbsCount($user_id, $type);
      }

      $this->load->model('email_queue');
      $this->email_queue->save($vote_id, 'vote', (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''));
    }

    render_json_response(array('success'));
  }

  /**
   * function search
   *
   * show search option
   *
   */
  function search($store_id)
  {
    $widget_user = session_get_widget_user();
    may_require_exit('failure', ($widget_user['user_id'] && trim($store_id)));

    $data = $this->load->view('widget/search_popup', array(), true);

    render_json_response($data);
  }
  
  /**
   * function categories
   *
   * return list of categories & brands
   *
   */
  function categories($store_id)
  {
    $widget_user = session_get_widget_user();
    may_require_exit('failure', ($widget_user['user_id'] && trim($store_id)));

    $data['autonomous_count'] = $this->product->getAutonomousProductsCount($store_id);
    $data['categories'] = $this->category->getWidgetCategories($store_id);
    $data['brands'] = $this->brand->getWidgetBrands($store_id);

    $data = $this->load->view('widget/categories_list', $data, true);

    render_json_response($data);
  }

  /**
   * function categories
   *
   * return list of categories & brands
   *
   */
  function products($store_id, $ref_type, $ref_id, $parent = false)
  {
    $data['already_linked'] = explode(',', $_REQUEST['qawiki_products']);
    $data['already_linked'] = array_map('trim', $data['already_linked']);

    $widget_user = session_get_widget_user();

    $validate = (trim($store_id) && trim($ref_type) && trim($ref_id) && $widget_user && trim($widget_user['user_id']));
    may_require_exit('failure', $validate);

    if($ref_type == 'category')
    {
      if($parent)
      {
        $data['sub_categories'] = $this->category->getWidgetSubCategories($store_id, $ref_id);
        $response['sub_categories'] = $this->load->view('widget/sub_categories', $data, true);
      }
      
      $data['products'] = $this->product->getProductByCategoryId($ref_id);
    }
    elseif($ref_type == 'brand')
    {
      $data['products'] = $this->product->getProductByBrandId($ref_id);
    }
    else
    {
      $data['products'] = $this->product->getAutonomousProducts($store_id);
    }

    $response['products'] = $this->load->view('widget/products_list', $data, true);

    render_json_response($response);
  }
  
  /**
   * function search_products
   *
   * @param <int> $store_id
   * @param <string> $search_key
   * 
   * return list of products
   *
   */
  function search_products($store_id, $search_key)
  {    
    $data['already_linked'] = explode(',', $_REQUEST['qawiki_products']);
    $data['already_linked'] = array_map('trim', $data['already_linked']);

    $widget_user = session_get_widget_user();

    $validate = (trim($store_id) && trim($search_key) && $widget_user && trim($widget_user['user_id']));
    may_require_exit('failure', $validate);

    $data['products'] = $this->product->searchProduct($store_id, $search_key);//echo $this->db->last_query();

    $data = $this->load->view('widget/search_products', $data, true);

    render_json_response($data);
  }  

  /**
   *
   * Upload image to server
   *
   */
  function upload()
  {
    echo 2;
    echo '<script>self.close()</script>';
    exit;
  }

  function email()
  {
    $this->load->model('email_queue');
    $this->email_queue->process();

    exit;
  }
}