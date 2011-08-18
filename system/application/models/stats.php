<?php

/**
 * 
 * @package -   Stats
 * 
 * 
 * @author -    Kashif
 * 
 */

class Stats extends Model
{
  private $CI;
  private $time;
  private $exec_for_all;
  
  private $mapping = array();
  
  function  __construct()
  {
    parent::Model();
    
    $this->CI =& get_instance();
    $this->time = gmdate('Y-m-d H:i:s');
    $this->exec_for_all = true;

    $this->initializeMapping();
  }

  function getOne($store_id, $item_id, $item_type, $date)
  {
    $this->db->where('store_id', $store_id);
    $this->db->where('item_id', $item_id);
    $this->db->where('item_type', $item_type);
    $this->db->where('DATE(created_at)', $date);
    
    $row = $this->db->get('stats')->result_array();
    
    return ($row) ? $row[0] : null;
  }
  
  function save($data)
  {
    //$data['created_at'] = gmdate('Y-m-d H:i:s');
    //$data['updated_at'] = gmdate('Y-m-d H:i:s');
    
    $this->db->insert('stats', $data);
  }
  
  function update($data, $id)
  {
    $data['updated_at'] = gmdate('Y-m-d H:i:s');
    
    $this->db->where('id', $id);
    $this->db->update('stats', $data);
  }
  
  /**
   * 
   * function saveRow
   * 
   * @param <array> $data
   * @param <date>  $date
   * 
   * save or update db data
   * 
   */
  function saveRow($data, $date)
  {
    // check if record exists
    $row = $this->getOne($data['store_id'], $data['item_id'], $data['item_type'], $date);
    if(!$row)
    {
      $data['created_at'] = $date.' '.date('H:i:s');

      $this->save($data);
    }
    else
    {
      $this->update($data, $row['id']);
    }    
  }  
  
  /**
   * 
   * function initializeMapping
   * 
   * inititalize mapping array for db fields
   * 
   */
  function initializeMapping()
  {
    $this->mapping = array(
      'pending_question'  => 'questions_pending',
      'pending_answer'    => 'answers_pending',
      'asnwered_q'        => 'answered_app_q',
      'prod_asnwered_q'   => 'prod_ans_app_q',
      'sub_categories_q'  => 'categories_submit_q'
    );
  }
  
  function getMapping($key)
  {
    return isset($this->mapping[$key]) ? $this->mapping[$key] : null;
  }
  
  /**
   * 
   * function resetDataArray
   * 
   * @param <array>  $data
   * 
   */
  function resetDataArray($data)
  {
    $data['q_submitted_daily'] = 0;
    $data['questions_pending'] = 0;
    $data['submitted_answers'] = 0;
    $data['answers_pending'] = 0;
    $data['submitted_questions'] = 0;
    $data['answered_app_q'] = 0;
    $data['unanswered_app_q'] = 0;
    $data['products_submitted_q'] = 0;
    $data['prod_ans_app_q'] = 0;
    $data['prod_unans_app_q'] = 0;
    $data['categories_submit_q'] = 0;
    $data['percent_q_answered'] = 0;
    
    return $data;
  }
  
  /**
   * 
   * function whereTime
   * 
   * check exec_for_all flag to decide timestamp
   * 
   */
  function whereTime()
  {
    if(!$this->exec_for_all)
    {
      return ' AND created_at = "'.substr($this->time, 0, 10).'"';
    }
    
    return '';
  }
  
  /**
   * 
   * function process
   * 
   * init point
   * 
   */
  function process()
  {
    $this->CI->load->model('qa_product','product');
    $this->CI->load->model('qa_brand','brand');
    $this->CI->load->model('qa_catagory','category');
    $this->CI->load->model('posts','post');
    $this->CI->load->model('stores','store');
    
    $this->processCategories();
    
    $this->processBrands();
  }  
  
  /**
   * 
   * function processCategories
   * 
   * iterate over list of categories
   * 
   */
  function processCategories()
  {
    $offset = 0; 
    $limit = 500;
    
    do
    {
      $this->db->limit($limit);
      $this->db->offset($offset);
      
      $categories = $this->db->get('qa_category')->result_array();
      
      foreach($categories as $category)
      {
        $stats = $this->gatherStats($category, 'category');
        
        $this->processData($stats, $category);
      }
      
      $offset += $limit;
    }
    while($categories && count($categories) > 0);
  }
  
  /**
   * 
   * function processBrands
   * 
   * iterate over list of categories
   * 
   */
  function processBrands()
  {
    $offset = 0; 
    $limit = 500;
    
    do
    {
      $this->db->limit($limit);
      $this->db->offset($offset);
      
      $brands = $this->db->get('qa_brand')->result_array();
      
      foreach($brands as $brand)
      {
        $stats = $this->gatherStats($brand, 'brand');
        
        $this->processData($stats, $brand, 'brand');
      }
      
      $offset += $limit;
    }
    while($brands && count($brands) > 0);
  }
  
  /**
   * 
   * function gatherStats
   * 
   * @param <array> $stats
   * 
   */
  function gatherStats($item, $type)
  {
    $stats = array();
        
    $rows = $this->getDailySubmittedAndPendingPost($item, $type);
    $this->formatByDate($rows, 'status', $stats);

    $rows = $this->getAnsweredQuestions($item, $type);
    $this->formatByDate($rows, 'asnwered_q', $stats);

    $rows = $this->getProductsWithSubmittedQuestions($item, $type);
    $this->formatByDate($rows, 'status', $stats);

    $rows = $this->getProductsWithAnsweredQuestions($item, $type);
    $this->formatByDate($rows, 'prod_answered_q', $stats);

    if($type == 'category')
    {
      $rows = $this->getSubCategoriesWithSubmittedQuestions($item, $type);
      $this->formatByDate($rows, 'sub_categories_q', $stats);
    }

    if(!empty($stats))
    {
      /*echo '<pre>';
      print_r($stats);
      echo '</pre>';*/
    }
    
    return $stats;
  }
  
  /**
   * 
   * function processData
   * 
   * @param <array> $stats
   * @param <array> $item
   * @param <string> $item_type
   * 
   */
  function processData($stats, $item, $item_type = 'category')
  {
    if($stats)
    {
      $data = array(
        'store_id'               => $item['qa_store_id'],
        'item_id'                => $item['id'],
        'item_type'              => $item_type
      );

      // loop over catgeory stats
      foreach($stats as $date => $value)
      {
        $data = $this->resetDataArray($data);
        
        $data['updated_at'] = $date.' '.date('H:i:s');

        // loop over one day data
        foreach($value as $key => $val)
        {
          if(isset($val['type']))
          {
            if($val['type'] == 'question')
            {
              $data['q_submitted_daily'] += $val['cnt'];
              $data['submitted_questions'] += $val['cnt'];
            }
            else
            {
              $data['submitted_answers'] += $val['cnt'];
            }
          }
          elseif(in_array($key, array('valid_prod_q', 'pending_prod_q')))
          {
            $data['products_submitted_q'] += $val['cnt'];
          }
          
          if($this->getMapping($key))
          {
            $data[$this->getMapping($key)] = $val['cnt'];
          }
        }
        
        $this->getUnansweredQuestions($data, $value);
        $this->getProductsWithUnansweredQuestions($data, $value);
        $this->getPercentageQuestionsAnswered($data, $value);

        if(!empty($stats))
        {
          echo '<pre>';
          print_r($data);
          echo '</pre>';
        }
        
        $this->saveRow($data, $date);
      }
    }
  }
  
  /**
   * 
   * function formatByDate 
   * 
   */
  function formatByDate($stats, $field, &$data)
  {    
    foreach($stats as $key => $value)
    {
      if(!isset($data[$value['created_at']]['total']))
        $data[$value['created_at']]['total'] = 0;
      
      $index = $field;
      if(isset($value[$field]))
      {
        $index = $value[$field];
        
        if(isset($value['type']))
          $index .= '_'.$value['type'];
      } 
      
      $data[$value['created_at']][$index] = $value;
      $data[$value['created_at']]['total'] += $value['cnt'];
    }
  }
  
  /**
   * 
   * function getDailySubmittedAndPendingPost
   * 
   * @param <array>   $category
   * @param <string>  $type
   * 
   * return calculate total submitted and pending questions
   * 
   */
  function getDailySubmittedAndPendingPost($item, $type = 'category')
  {
    $query = '
      SELECT count(*) as cnt, DATE(qa_created_at) as created_at,
        CASE WHEN (mod_level <> 1 AND mod_status = "valid") THEN "valid" ELSE (CASE WHEN ((mod_level = 1 AND mod_status = "valid") OR mod_status IS NULL) THEN "pending" ELSE "invalid" END) END AS status,
        CASE WHEN (qa_parent_id = 0) THEN "question" ELSE "answer" END AS type
      FROM store_item_posts
      WHERE qa_ref_id = '.$item['id'].' AND
        qa_post_type = "'.$type.'"
        '.$this->whereTime().'
      GROUP BY type, status, created_at
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * 
   * function getAnsweredQuestions
   * 
   * @param <array>   $category
   * @param <string>  $type
   * 
   * return calculate total submitted and pending questions
   * 
   */
  function getAnsweredQuestions($item, $type = 'category')
  {
    $query = '
      SELECT count(distinct qa_parent_id) as cnt, DATE(qa_created_at) as created_at
      FROM store_item_posts
      WHERE qa_ref_id = '.$item['id'].' AND
        qa_post_type = "'.$type.'" AND
        qa_parent_id > 0
        '.$this->whereTime().'
      GROUP BY created_at
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * 
   * function getProductsWithSubmittedQuestions
   * 
   * @param <array>   $category
   * @param <string>  $type
   * 
   * return valid and pending question within all products of a category
   * 
   */
  function getProductsWithSubmittedQuestions($item, $type = 'category')
  {
    /*$query = '
      SELECT count(distinct qa_post_id) as cnt, DATE(qa_created_at) as created_at,
        CASE WHEN (mod_level <> 1 AND mod_status = "valid") THEN "valid_prod_q" ELSE "pending_prod_q" END AS status
      FROM store_item_posts
      WHERE qa_parent_id = 0 AND
        qa_post_type = "product" AND
        qa_ref_id IN(
          SELECT id
          FROM qa_product
          WHERE qa_'.$type.'_id = '.$item['id'].'
        )
        '.$this->whereTime().'
      GROUP BY status, created_at
    ';*/
    
    $query = '
      SELECT count(distinct p.id) as cnt, DATE(q.qa_created_at) as created_at,
        CASE WHEN (q.mod_level <> 1 AND q.mod_status = "valid") THEN "valid_prod_q" ELSE (CASE WHEN ((q.mod_level = 1 AND q.mod_status = "valid") OR q.mod_status IS NULL) THEN "pending_prod_q" ELSE "invalid_prod_q" END) END AS status
      FROM qa_product p
      INNER JOIN store_item_posts q ON p.id = q.qa_ref_id AND q.qa_post_type = "product" AND q.qa_parent_id = 0
      WHERE qa_'.$type.'_id = '.$item['id'].'
        '.$this->whereTime().'
      GROUP BY status, created_at
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * 
   * function getProductsWithAnsweredQuestions
   * 
   * @param <array>   $category
   * @param <string>  $type
   * 
   * return answered question within all products in a category or brand
   * 
   */
  function getProductsWithAnsweredQuestions($item, $type = 'category')
  {
    /*$query = '
      SELECT count(distinct qa_parent_id) as cnt, DATE(qa_created_at) as created_at
      FROM store_item_posts
      WHERE qa_parent_id > 0 AND
        qa_post_type = "product" AND
        qa_ref_id IN(
          SELECT id
          FROM qa_product
          WHERE qa_'.$type.'_id = '.$item['id'].'
        )
        '.$this->whereTime().'
      GROUP BY created_at
    ';*/
    
    $query = '
      SELECT count(distinct p.id) as cnt, DATE(q.qa_created_at) as created_at
      FROM qa_product p
      INNER JOIN store_item_posts q ON p.id = q.qa_ref_id AND q.qa_post_type = "product" AND q.qa_parent_id > 0
      WHERE qa_'.$type.'_id = '.$item['id'].'
        '.$this->whereTime().'
      GROUP BY created_at
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * 
   * function getSubCategoriesWithSubmittedQuestions
   * 
   * @param <array>   $category
   * @param <string>  $type
   * 
   * return valid and pending question within all products of a category
   * 
   */
  function getSubCategoriesWithSubmittedQuestions($item)
  {
    /*$query = '
      SELECT count(distinct qa_parent_id) as cnt, DATE(qa_created_at) as created_at
      FROM store_item_posts
      WHERE qa_parent_id = 0 AND
        qa_post_type = "category" AND
        qa_ref_id IN(
          SELECT id
          FROM qa_category
          WHERE qa_parent_id = '.$item['id'].'
        )
        '.$this->whereTime().'
      GROUP BY created_at
    ';*/
    
    $query = '
      SELECT count(distinct c.id) as cnt, DATE(q.qa_created_at) as created_at
      FROM qa_category c
      INNER JOIN store_item_posts q ON c.id = q.qa_ref_id AND q.qa_post_type = "category" AND q.qa_parent_id = 0
      WHERE c.qa_parent_id = '.$item['id'].'
        '.$this->whereTime().'
      GROUP BY created_at
    ';
    
    return $this->db->query($query)->result_array();
  }  
  
  /**
   * 
   * function getUnansweredQuestions
   * 
   * @param <array>   $data
   * @param <array>  $value
   * 
   * calculate total unanswered questions
   * 
   */
  function getUnansweredQuestions(&$data, $value)
  {
    $data['unanswered_app_q'] = 0;
    
    if(isset($value['valid_question']))
    {
      if(isset($data['answered_app_q']))
      {
        $data['unanswered_app_q'] = $value['valid_question']['cnt'] - $data['answered_app_q'];
      }
      else
      {
        $data['unanswered_app_q'] = $value['valid_question']['cnt'];
      }
    }
  }
  
  /**
   * 
   * function getProductsWithUnansweredQuestions
   * 
   * @param <array>   $data
   * @param <array>  $value
   * 
   * calculate total unanswered questions within all products in a category
   * 
   */
  function getProductsWithUnansweredQuestions(&$data, $value)
  {
    $data['prod_unans_app_q'] = 0;
    
    if(isset($value['valid_prod_q']))
    {
      if(isset($data['prod_ans_app_q']))
      {
        $data['prod_unans_app_q'] = $value['valid_prod_q']['cnt'] - $data['prod_ans_app_q'];
      }
      else
      {
        $data['prod_unans_app_q'] = $value['valid_prod_q']['cnt'];
      }
    }
  }

  /**
   * 
   * function getPercentageQuestionsAnswered
   * 
   * @param <array>   $data
   * @param <array>  $value
   * 
   * calculate percentage of questions answered
   * 
   */
  function getPercentageQuestionsAnswered(&$data, $value)
  {
    $data['percent_q_answered'] = 0;
    
    if($data['q_submitted_daily'] > 0)
    {
      $data['percent_q_answered'] = round(($data['answered_app_q'] / $data['q_submitted_daily']) * 100, 2);
    }
  }

  /**
   * 
   * function getStats
   * 
   * @param <array>   $categories
   * @param <array>   $brands
   * @param <array>   $fields
   * 
   * calculate daily stats 
   * 
   */
  function getStats($categories, $brands, $fields, $start_date, $end_date)
  {
    foreach($fields as &$field)
    {
      $field = 'SUM('.$field.') as '.$field;
    }
    
    $fields = join(',', $fields);
    
    $this->db->select($fields);
    if(!empty($categories))
    {
      $this->db->where('(item_id IN ('.join(',', $categories).') AND item_type = "category")');
    }
    if(!empty($brands))
    {
      $this->db->or_where('(item_id IN ('.join(',', $brands).') AND item_type = "brand")');
    }
    
    $this->db->where('DATE(created_at) BETWEEN "'.date('Y-m-d', $start_date).'" AND "'.date('Y-m-d', $end_date).'"');
    
    return $this->db->get('stats')->result_array();
  }  
}
