<?php

/**
 * 
 * @package - moderate
 * 
 * @author - Kashif
 * 
 */

class Moderate extends qaController
{
  function __construct()
  {
    parent::__construct();
    
    $this->load->model('post_products', 'linked_products');
    $this->load->model('qa_product', 'product');
    $this->load->model('qa_brand', 'brand');
    $this->load->model('qa_catagory', 'category');
    $this->load->model('qa_teams', 'team');
    $this->load->model('qa_team_members', 'team_member');
    $this->load->model('moderation', 'moderation_m');
    $this->load->model('user_designations');
    
    $this->store_id = $this->uri->segment(3);
    $user_role = Permissions::can_edit($this->store_id, $this->uid);

    if($user_role == 'view')
    {
      redirect('catalog/index/'.$this->store_id);
    }
    
    $this->store_data = $this->store->getStoreById($this->store_id);
  }
  
  /**
   * 
   * function index
   * 
   * 
   */
  function index($store_id, $item_id = null, $item_type = null)
  {
    $this->store_slot = array(
      'store'       => $this->store_data[0],
      'sub_links'   => get_sub_links('settings'),
      'selected'    => 'moderate'
    );
    
    $params = parse_pagination_params();
    
    $data['post'] = $this->moderation_m->get($this->store_id, null, $item_id, $item_type, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->moderation_m->getCount($this->store_id, $item_id, $item_type);
    
    $data['designations'] = $this->user_designations->get_designation_name($store_id);                
    $data["designations"] = get_designations($data["designations"]);
    
    pagination_calculate_pages($params);
    
    $data['params'] = $params;
    
    $data['item_id'] = $item_id;
    $data['item_type'] = $item_type;

    $this->load->view('moderate/index', $data);
  }
  
  /**
   * 
   * function answersList
   * 
   * 
   */
  function answersList($store_id, $question_id)
  {
    $this->no_layout = true;
    
    $data['post'] = $this->moderation_m->getAnswers($question_id);
    
    $this->load->view('moderate/answersList', $data);
  }
  
  /**
   * 
   * function questionHistory
   * 
   * 
   */
  function questionHistory($store_id, $question_id)
  {
    $this->no_layout = true;
    
    $this->load->model('post_history', 'history_m');
    
    $data['history'] = $this->history_m->get($question_id);
    
    $this->load->view('components/_history', $data);
  }
  
  /**
   * 
   * function paginate
   * 
   * 
   */
  function paginate($store_id, $item_id = null, $item_type = null)
  {
    $this->no_layout = true;
    
    $search_term = trim($this->input->post('term'));
    $start_date = trim($this->input->post('start_date'));
    $end_date = trim($this->input->post('end_date'));
    $items_filter = $this->input->post('items_filter');
    $sort_by = $this->input->post('sort_by');
    
    $params = parse_pagination_params();
    
    $post = $this->moderation_m->get($this->store_id, null, $item_id, $item_type, $params['offset'], $params['rec_per_page'], $search_term, $start_date, $end_date, $items_filter, true, $sort_by);

    $params['total_records'] = $this->moderation_m->getCount($this->store_id, $item_id, $item_type, $search_term, $start_date, $end_date, $items_filter, true);
    
    pagination_calculate_pages($params);
    
    $data['data'] = $this->load->view('moderate/_postList', array('post' => $post), true);
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    echo json_encode($data);
    exit;
  }
  
  /**
   * 
   * function saveCanModerate
   * 
   * 
   */
  function saveCanModerate()
  {
    $post_id = $this->input->post('post_id');
    $can_moderate = $this->input->post('can_moderate');
    
    $this->post->updatePost($post_id, array(
      'can_moderate' => $can_moderate
    ));    
    exit('1');
  }
  
  /**
   * 
   * function postSuggest
   * 
   * 
   */
  function postSuggest()
  {
    $data = array();
    
    $search_term = trim($this->input->post('term'));
    
    if($search_term)
    {
      $rows = $this->post->suggest($this->store_id, $search_term);

      if($rows)
      {
        foreach($rows as $row)
        {
          $data[] = array(
            'Id'     => $row['qa_post_id'],
            'Value'  => $row['qa_title']
          );
        }
      }
    }
    
    render_json_response($data);
  }
  
  /**
   * 
   * function answerDialog
   * 
   * 
   */
  function answerDialog($store_id, $question_id)
  {
    $this->no_layout = true;
    
    $data['post'] = $this->post->getPostById($question_id);
    
    $this->load->view('moderate/answerDialog', $data);    
  }
  
  /**
   * 
   * function saveAnswer
   * 
   * 
   */
  function saveAsnwer()
  {
    $this->no_layout = true;
    
    $question_id = $this->input->post('question_id');
    
    if($question_id)
    {    
      $question = $this->post->getPostById($question_id);
      $question = $question[0];
    
      $answer = array(
        'qa_store_id'     => $this->store_id,
        'qa_ref_id'       => $question->qa_ref_id,
        'qa_post_type'    => $question->qa_post_type,
        'qa_user_id'      => $this->uid,
        'qa_title'        => parse_href_tags(trim($this->input->post('answer-text'))),
        'qa_parent_id'    => $question->qa_post_id,
        'qa_created_at'   => gmdate('Y-m-d H:i:s'),
        'mod_status'      => 'valid'
      );
      
      $this->post->addPost($answer);
      
      $data['similarPost'] = $this->post->similarPosts($question->qa_ref_id, $question->qa_post_type, $question->qa_post_id);
    }
    
    $this->load->view('components/_similarPost', $data);
  }
  
  /**
   * 
   * function changeModStatus
   * 
   * 
   * change the status of question(s)
   * 
   */
  function changeModStatus()
  {
    $questions = $this->input->post('questions');
    
    $status = $this->input->post('status');
    $response = 'Rejected';
    
    if($status == 'valid')
    {
      $response = 'Approved';
    }
    
    $this->post->changeModStatus($this->store_id, $questions, $status, $this->current_user_name, $response);
    
    exit($response);
  }

  /**
   * 
   * function export
   * 
   * 
   * @param <int>   $store_id
   * @param <int>   $question_id
   * 
   */
  function export($store_id, $question_id, $type="")
  {
    require_once APPPATH . 'libraries/qaExcelExport.class.php';
    
    $this->load->model('post_history', 'history_m');
    
    $question_id = explode(':', $question_id);
    
    // questions info
    $questions = $this->moderation_m->get($this->store_id, $question_id);
    
    foreach($questions as $key => &$question)
    {
      // get answers
      $question['answers'] = $this->moderation_m->getAnswers($question['qa_post_id']);
      // get history
      $question['history'] = $this->history_m->get($question['qa_post_id']);
    }
    
    $data['questions'] = $questions;
    
    $this->no_layout = true;
    qaExcelExport::header($type);
    
    $this->load->view('moderate/export', $data);
  }
  
  /**
   * 
   * function export
   * 
   * 
   * @param <int>   $store_id
   * @param <int>   $question_id
   * 
   */
  function sendEmail($store_id, $question_id)
  {
    $this->load->model('post_history', 'history_m');
    
    $emails = trim($this->input->post('emails'));
    if(!$emails)
    {
      exit('0');
    }
    
    $emails = explode(',', $emails);
    
    // questions info
    $questions = $this->moderation_m->get($this->store_id, array($question_id));
    
    if($questions)
    {
      // get answers
      $questions[0]['answers'] = $this->moderation_m->getAnswers($questions[0]['qa_post_id']);
      // get history
      $questions[0]['history'] = $this->history_m->get($questions[0]['qa_post_id']);
    
      $data['questions'] = $questions;
      // get contents
      $contents = $this->load->view('moderate/export', $data, true);
    
      foreach($emails as $email)
      {
        if(!trim($email))
          continue;

        mail($email, 'Question report', $contents, 'Content-Type:text/html');
      }
    }
    
    exit('1');
  }
}

