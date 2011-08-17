<?php


/**
 * 
 * @package - moderation
 *
 * @author - Kashif
 */
class moderation extends Model
{
  function moderation()
  {
    parent::Model();    
  }
  
  function post_query_filters($filter)
  {
    $order_by = 'vote_temp.pos_vote desc, vote_temp.neg_vote desc';
    $filter = strtolower(trim($filter));
    
    $mapping = array(
      'helpful'   => 'vote_temp.pos_vote desc, vote_temp.neg_vote desc',
      'recentq'   => 'q.qa_created_at desc',
      'oldestq'   => 'q.qa_created_at asc',
      'recenta'   => 'answer_date desc',
      'oldesta'   => 'answer_date asc',
      'answers'   => 'answers_count desc',
      'noanswers' => 'answers_count asc'
    );
    
    if(!isset($mapping[$filter]))
      $filter = 'helpful';
    
    $order_by = $mapping[$filter];
    
    $case = 'CASE WHEN (MAX(a.qa_created_at) IS NOT NULL) THEN '.($filter == 'oldesta' ? 'MIN(a.qa_created_at) ELSE NOW()' : 'MAX(a.qa_created_at) ELSE ""').' END AS answer_date';

    //$order_by = 'q.qa_post_type asc, '.$order_by;

    return array($order_by, $case);
  }
  
  /**
   * function get
   * 
   * @param - $store_id
   * @param - $offset
   * @param - $limit
   * 
   */
  function get($store_id, $post_ids = null, $item_id = null, $item_type = null, $offset = 0, $limit = 5, $search_term = null, $start_date = null, $end_date = null, $items_filter = array(), $check_items = false, $sort_by = 'helpful')
  {
    if($check_items && empty($items_filter))
      return array();
    
    list($order_by, $case) = $this->post_query_filters($sort_by);
    
    $query = '
      SELECT q.*, count(a.qa_post_id) as answers_count, vote_temp.pos_vote, vote_temp.neg_vote, u.name as user_name, count(spam.post_id) as spam_count,
        '.$case.'
      FROM qa_post q
      LEFT JOIN qa_post a ON a.qa_parent_id = q.qa_post_id AND a.mod_status = "valid"
      LEFT JOIN (
        SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
        FROM post_vote
        GROUP BY post_id
      ) as vote_temp ON vote_temp.post_id = q.qa_post_id
      INNER JOIN qa_user as u ON u.qa_user_id = q.qa_user_id
      LEFT JOIN post_spam as spam ON q.qa_post_id = spam.post_id
      WHERE q.qa_store_id = ' . $store_id . '
        AND q.qa_parent_id = 0
        '.($item_id ? ' AND q.qa_ref_id = ' . $item_id . ' AND q.qa_post_type = "' . $item_type . '"' : '').'
        '.$this->filterParams($search_term, $start_date, $end_date, $items_filter).'
        '.($post_ids ? ' AND q.qa_post_id IN ('.join(',', $post_ids).')' : '').'
      GROUP BY q.qa_post_id
        '.(trim($order_by) ? 'ORDER BY '.$order_by : '').'
      LIMIT '.$offset.', '.$limit.'
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * function getCount
   * 
   * @param - $store_id
   * 
   */
  function getCount($store_id, $item_id = null, $item_type = null, $search_term = null, $start_date = null, $end_date = null, $items_filter = array(), $check_items = false)
  {
    if($check_items && empty($items_filter))
      return 0;
    
    $query = '
      SELECT count(q.qa_post_id) as cnt
      FROM qa_post q
      INNER JOIN qa_user as u ON u.qa_user_id = q.qa_user_id
      WHERE q.qa_store_id = ' . $store_id . '
        AND q.qa_parent_id = 0
        '.($item_id ? ' AND q.qa_ref_id = ' . $item_id . ' AND q.qa_post_type = "' . $item_type . '"' : '').'
        '.$this->filterParams($search_term, $start_date, $end_date, $items_filter);
    ;
    
    $row = $this->db->query($query)->result_array();
    
    return ($row) ? $row[0]['cnt'] : 0;
  }
 
  /**
   * 
   * function get
   * 
   * @param - $store_id
   * @param - $offset
   * @param - $limit
   * 
   */
  function getAnswers($question_id, $offset = 0, $limit = 20)
  {    
    $query = '
      SELECT a.*, u.name as user_name
      FROM qa_post a
      INNER JOIN qa_user as u ON u.qa_user_id = a.qa_user_id
      WHERE a.qa_parent_id = ' . $question_id . '
      LIMIT '.$offset.', '.$limit.'
    ';
    
    return $this->db->query($query)->result_array();
  }
  
  /**
   * function filterParams
   * 
   * @param <string>  $search_term
   * @param <date>    $start_date
   * @param <date>    $end_date
   * @param <array>   $items_filter
   * 
   */
  function filterParams($search_term, $start_date, $end_date, $items_filter)
  {
    $filter = array();
    
    if($search_term)
    {
      $filter[] = 'q.qa_title LIKE "'.mysql_escape_string($search_term).'%"';
    }
    
    if($start_date)
    {
      $filter[] = 'DATE(q.qa_created_at) >= "'.$start_date.'"';
    }
    
    if($end_date)
    {
      $filter[] = 'DATE(q.qa_created_at) <= "'.$end_date.'"';
    }
    
    if(is_array($items_filter) && !empty($items_filter))
    {
      $map = array(
        'category'      => 'q.qa_post_type',
        'brand'         => 'q.qa_post_type',
        'product'       => 'q.qa_post_type',
        'widget'        => 'u.type'
      );
      
      foreach($items_filter as &$value)
      {
        $value = $map[$value].' = "'.$value.'"';
      }
      
      //$filter[] = join(' OR ', $items_filter);
      $filter[] = '('.join(' OR ', $items_filter).')';
    }
    
    return (!empty($filter)) ? ' AND '.join(' AND ', $filter) : '';
  }
}