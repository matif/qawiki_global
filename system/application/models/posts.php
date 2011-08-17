<?php

/**
 * Description of posts
 *
 * @author purelogics
 */

class Posts extends Model
{
  public static $recent_tab_content;
  
  //put your code here
  function Posts()
  {
    parent::Model();
    
    $date = gmdate('Y-m-d');
    $timestamp = time();
    
    self::$recent_tab_content = array(
      '5_questions_within_10_days'      =>  array(5, 'DATE(p.qa_created_at) BETWEEN "'.gmdate('Y-m-d', strtotime('-10 days', $timestamp)).'" AND "'.$date.'"'),
      '10_questions_within_15_days'     =>  array(10, 'DATE(p.qa_created_at) BETWEEN "'.gmdate('Y-m-d', strtotime('-15 days', $timestamp)).'" AND "'.$date.'"'),
      '10_questions_within_one_month'   =>  array(10, 'DATE(p.qa_created_at) BETWEEN "'.gmdate('Y-m-d', strtotime('-1 month', $timestamp)).'" AND "'.$date.'"')
    );
  }
  
  
  /**
   * 
   * function get_recent_tab_filter
   * 
   * @param $filter
   * 
   */
  function get_recent_tab_filter($filter)
  {
    if(isset(self::$recent_tab_content[$filter]))
    {
      $CI =& get_instance();
      $CI->config->set_item('WIDGET_QUESTION_PER_PAGE', self::$recent_tab_content[$filter][0]);
      
      return ' AND '.self::$recent_tab_content[$filter][1];
    }
    
    return '';
  }
  
  /**
   * 
   * function post_query_filters
   * 
   * 
   */

  function post_query_filters($filter)
  {
    $order_by = 'vote_temp.pos_vote desc, vote_temp.neg_vote desc';
    $having = '';
    $filter = strtolower(trim($filter));
    
    if($filter == 'unanswered')
    {
      $having = 'total_answers = 0';
    }
    
    $mapping = array(
      'helpful'   => 'vote_temp.pos_vote desc, vote_temp.neg_vote desc',
      'recentq'   => 'p.qa_created_at desc',
      'oldestq'   => 'p.qa_created_at asc',
      'recenta'   => 'answer_date desc',
      'oldesta'   => 'answer_date asc',
      'answers'   => 'total_answers desc',
      'noanswers' => 'total_answers asc'
    );
    
    if(!isset($mapping[$filter]))
      $filter = 'helpful';
    
    $order_by = $mapping[$filter];
    
    $case = 'CASE WHEN (MAX(a.qa_created_at) IS NOT NULL) THEN '.($filter == 'oldesta' ? 'MIN(a.qa_created_at) ELSE NOW()' : 'MAX(a.qa_created_at) ELSE ""').' END AS answer_date';

    $order_by = 'p.qa_post_type asc, '.$order_by;

    return array($order_by, $having, $case);
  }

  function addPost($data)
  {
    $this->db->insert('qa_post', $data);
    
    if(isset($data['qa_parent_id']) && $data['qa_parent_id'] > 0)
    {
      Post_history::saveHistory($data['qa_parent_id'], 'answer', $data['qa_user_id'], true);
    }
    
    return $this->db->insert_id();
  }

  function getPost($ref_id, $ref_type, $post_id = 0, $offset = 0, $limit = 10, $mod_level = -1, $mod_status = 'valid', $user_id = 0, $sort_column = 'qa_post_id', $sort_order = 'asc')
  {
    if(!trim($user_id))
      $user_id = 0;

    $this->db->where('p.qa_ref_id', $ref_id);
    $this->db->where('p.qa_post_type', $ref_type);
    
    if($post_id == -1)
      $this->db->where('p.qa_parent_id > 0');
    else
      $this->db->where('p.qa_parent_id', $post_id);

    if($mod_level > -1)
    {
      if($mod_level == -2)
        $this->db->where('p.mod_level <> 1');
      else
        $this->db->where('p.mod_level', $mod_level);
    }
    
    $this->db->orderby($sort_column.' '.$sort_order);

    if($mod_status == 'IS NULL')
      $this->db->where('p.mod_status IS NULL');
    else
      $this->db->where('p.mod_status', $mod_status);

    if($user_id > -1)
    {
      $this->db->select('p.qa_post_id, p.qa_title, p.qa_description, p.qa_created_at, p.qa_user_id, u.name, v.vote_id,p.image_url,p.video_url, p.video_caption, p.qa_parent_id');
      
      $this->db->join('qa_user as u', 'u.qa_user_id = p.qa_user_id', 'inner');
      $this->db->join('post_vote as v', 'v.post_id = p.qa_post_id  AND v.user_id = '.$user_id, 'left');
    }
    else
    {
      $this->db->select('p.qa_post_id, p.qa_title, p.qa_description, p.qa_created_at, p.qa_user_id, p.image_url,p.video_url, p.video_caption, p.qa_parent_id');
    }
    
    $this->db->offset($offset);
    $this->db->limit($limit);
    
    $result = $this->db->get('qa_post as p')->result_array();
    
    return  $result;
  }
  
  function getPaginatePostCount($ref_id, $ref_type, $post_id = 0, $mod_level = -1, $mod_status = 'valid')
  {
    $this->db->select('count(*) as cnt');
    
    $this->db->where('qa_ref_id', $ref_id);
    $this->db->where('qa_post_type', $ref_type);
    
    if($post_id == -1)
      $this->db->where('qa_parent_id > 0');
    else
      $this->db->where('qa_parent_id', $post_id);

    if($mod_level > -1)
    {
      if($mod_level == -2)
        $this->db->where('mod_level <> 1');
      else
        $this->db->where('mod_level', $mod_level);
    }

    if($mod_status == 'IS NULL')
      $this->db->where('mod_status IS NULL');
    else
      $this->db->where('mod_status', $mod_status);
    
    $result = $this->db->get('qa_post')->result_array();
    
    return $result[0]['cnt'];
  }

  
  function deletePost($id)
  {
    $sqlQuery = "DELETE FROM qa_post WHERE qa_post_id = $id LIMIT 1";
    $this->db->query($sqlQuery);
  }

  function getPostCount($store_id)
  {
    $sqlQuery = "SELECT COUNT(qa_post_id) as CNT FROM qa_post WHERE qa_store_id = $store_id";
    $result = $this->db->query($sqlQuery)->result_array();

    return $result[0]['CNT'];
  }

  function postDetails ($user_id, $ref_id, $ref_type, $filter = '', $offset = 0, $limit = 5, $search_text = '')
  {
    if(!trim($user_id))
      $user_id = 0;

    if(trim($search_text))
    {
      $search_text = $this->search_criteria($search_text);
    }

    list($order_by, $having, $case, $where) = $this->post_query_filters($filter);

    $query = '
      SELECT p.*, count(a.qa_post_id) as total_answers, v.vote_id, vote_temp.pos_vote, vote_temp.neg_vote, u.name,
        '.$case.'
      FROM qa_post p
      LEFT JOIN qa_post a ON a.qa_parent_id = p.qa_post_id AND a.mod_status = "valid"
      LEFT JOIN post_vote v ON v.post_id = p.qa_post_id AND v.user_id = '.$user_id.'
      LEFT JOIN (
        SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
        FROM post_vote
        GROUP BY post_id
      ) as vote_temp ON vote_temp.post_id = p.qa_post_id
      INNER JOIN qa_user as u ON u.qa_user_id = p.qa_user_id
      WHERE p.qa_ref_id = ' . $ref_id . '
        AND p.qa_post_type = "' . $ref_type . '"
        AND p.qa_parent_id = 0
        AND p.mod_status = "valid"
        AND p.mod_level <> 1
        '.(trim($search_text) ? ' AND ('.$search_text.')' : '').'
        '.$this->get_recent_tab_filter($filter).'
      GROUP BY p.qa_post_id
      '.(trim($having) ? 'HAVING '.$having : '').'
      '.(trim($order_by) ? 'ORDER BY '.$order_by : '').'
      LIMIT '.$offset.', '.$limit.'
    ';//echo '<pre>'.$query.'</pre>';

    return $this->db->query($query)->result_array();
  }

  /**
   * function postDetailsCount
   *
   * @param <int> $ref_id
   * @param <string> $ref_type
   * @param <string> $filter
   *
   * return count of questions found accroding to custom criteria
   */
  function postDetailsCount ($ref_id, $ref_type, $filter = '', $post_id = 0, $search_text = '')
  {
    $filter = strtolower(trim($filter));
    if(!in_array($filter, array('answered', 'unanswered')))
    {
      $query = '
        SELECT count(*) as total_quest
        FROM qa_post p
        WHERE p.qa_ref_id = ' . $ref_id . '
          AND p.qa_post_type = "' . $ref_type . '"
          AND p.qa_parent_id = '.$post_id.'
          AND p.mod_status = "valid"
          AND p.mod_level <> 1
      ';
    }
    elseif ($filter == 'answered')
    {
      $query = '
        SELECT count(distinct p.qa_parent_id) as total_quest
        FROM qa_post p
        WHERE p.qa_ref_id = ' . $ref_id . '
          AND p.qa_post_type = "' . $ref_type . '"
          AND p.qa_parent_id > 0
          AND p.mod_status = "valid"
          AND p.mod_level <> 1
      ';
    }
    else
    {
      $query = '
        SELECT count(p.qa_post_id) as total_quest
        FROM qa_post p
        WHERE p.qa_ref_id = ' . $ref_id . '
          AND p.qa_post_type = "' . $ref_type . '"
          AND p.qa_parent_id = 0
          AND p.mod_status = "valid"
          AND p.mod_level <> 1
          AND p.qa_post_id NOT IN (
            SELECT distinct(qa_parent_id) as qa_parent_id
            FROM qa_post
            WHERE qa_ref_id = ' . $ref_id . '
              AND qa_post_type = "' . $ref_type . '"
              AND qa_parent_id > 0
              AND mod_status = "valid"
              AND mod_level <> 1
          )
      ';
    }

    if(trim($search_text))
    {
      $search_text = $this->search_criteria($search_text, 'qa_title');

      $query .= (trim($search_text) ? ' AND ('.$search_text.')' : '');
    }
    
    $query .= $this->get_recent_tab_filter($filter);

    $result = $this->db->query($query)->result_array();

    return $result[0]['total_quest'];
  }

  function getPostById($post_id,$paren_id ='')
  {
     $sqlQuery = "SELECT * FROM qa_post WHERE qa_post_id = $post_id";     
     $result = $this->db->query($sqlQuery)->result();
     if($result == NULL)
       $result = 0;
     return $result;
  }
  function updatePost($post_id , $data)
  {
    $this->db->where('qa_post_id',$post_id);
    $this->db->update('qa_post',$data);
  }
  function updateModeratoin($id, $mod_status, $mod_level)
  {
    $this->db->where('qa_post_id', $id);
    $this->db->set('mod_status', $mod_status);

    if($mod_level > 0)
    {
      $this->db->set('mod_level', $mod_level);
    }

    $this->db->update('qa_post');    
  }
  function getSpamPostCategory($id , $parent_id = 0, $offset = 0, $limit = 100)
  {
     $sql = "SELECT `p`.`qa_post_id` AS post_id, `p`.`qa_title`, `p`.`qa_description`,`p`.`mod_status`,
    `p`.`qa_created_at`, `p`.`qa_user_id`, `p`.`image_url`, `cat`.`qa_category_name`,`s`.`id`,cat.qa_category_id
    FROM `qa_store` as store
    INNER JOIN `qa_category` as cat ON `store`.`qa_store_id` = `cat`.`qa_store_id`
    INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `cat`.`id` AND p.qa_post_type = 'category' AND `p`.`mod_status` = 'valid'";
     if($parent_id != 0)
     {
      $sql.= "AND p.qa_parent_id > 0 ";
     }
     else
     {
       $sql.="AND  `p`.`qa_parent_id` = 0 ";
     }
    $sql .="INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
    WHERE `store`.`qa_store_id` = $id    
    GROUP BY `p`.`qa_post_id` 
    LIMIT $offset,$limit";
    return $this->db->query($sql)->result_array();
  }

  function getSpamPostCategoryCount($id , $parent_id = 0)
  {
    $sql = "
      SELECT count(`p`.`qa_post_id`)AS CNT
      FROM `qa_store` as store
      INNER JOIN `qa_category` as cat ON `store`.`qa_store_id` = `cat`.`qa_store_id`
      INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `cat`.`id` AND p.qa_post_type = 'category' AND `p`.`mod_status` = 'valid'";

    if($parent_id != 0)
    {
      $sql.= "AND p.qa_parent_id > 0 ";
    }
    else
    {
      $sql.="AND  `p`.`qa_parent_id` = 0 ";
    }

    $sql .="
      INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
      WHERE `store`.`qa_store_id` = $id";

    $res = $this->db->query($sql)->result_array();

    return isset($res[0]) ? $res[0]['CNT'] : 0;
  }

  function getSpamPostBrand($id , $parent_id = 0, $offset = 0, $limit = 100)
  {
     $sql = "SELECT `p`.`qa_post_id` AS post_id, `p`.`qa_title`, `p`.`qa_description`,`p`.`mod_status`,
    `p`.`qa_created_at`, `p`.`qa_user_id`, `p`.`image_url`,`s`.`id`,brand.qa_brand_id,brand.qa_brand_name
    FROM `qa_store` as store
    INNER JOIN `qa_brand` as brand ON `store`.`qa_store_id` = `brand`.`qa_store_id`
    INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `brand`.`id` AND p.qa_post_type = 'brand' AND `p`.`mod_status` = 'valid'";
     if($parent_id != 0)
     {
      $sql.= "AND p.qa_parent_id > 0 ";
     }
     else
     {
       $sql.="AND  `p`.`qa_parent_id` = 0 ";
     }
    $sql .= "INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
    WHERE `store`.`qa_store_id` = $id
    GROUP BY `p`.`qa_post_id`
    LIMIT $offset,$limit";
    return $this->db->query($sql)->result_array();
  }

  function getSpamPostBrandCount($id , $parent_id = 0)
  {
     $sql = "SELECT count(`p`.`qa_post_id`)AS CNT
    FROM `qa_store` as store
    INNER JOIN `qa_brand` as brand ON `store`.`qa_store_id` = `brand`.`qa_store_id`
    INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `brand`.`id` AND p.qa_post_type = 'brand' AND `p`.`mod_status` = 'valid'";
     if($parent_id != 0)
     {
      $sql.= "AND p.qa_parent_id > 0 ";
     }
     else
     {
       $sql.="AND  `p`.`qa_parent_id` = 0 ";
     }
    $sql .= "INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
    WHERE `store`.`qa_store_id` = $id";    
    $res = $this->db->query($sql)->result_array();
    return isset($res[0])?$res[0]['CNT']:0;
  }

  function getSpamPostProduct($id , $parent_id = 0, $offset = 0, $limit = 100)
  {
     $sql = "SELECT `p`.`qa_post_id` AS post_id, `p`.`qa_title`, `p`.`qa_description`,`p`.`mod_status`,
    `p`.`qa_created_at`, `p`.`qa_user_id`, `p`.`image_url`,`s`.`id`,product.qa_product_id,product.qa_product_title
    FROM `qa_store` as store
    INNER JOIN `qa_product` as product ON `store`.`qa_store_id` = `product`.`qa_store_id`
    INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `product`.`id` AND p.qa_post_type = 'product' AND `p`.`mod_status` = 'valid'";
     if($parent_id != 0)
     {
      $sql.= "AND p.qa_parent_id > 0 ";
     }
     else
     {
       $sql.="AND  `p`.`qa_parent_id` = 0 ";
     }
    $sql .= "INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
    WHERE `store`.`qa_store_id` = $id
    GROUP BY `p`.`qa_post_id`";
    
    return $this->db->query($sql)->result_array();;
  }
  
  function getSpamPostProductCount($id , $parent_id = 0)
  {
     $sql = "SELECT count(`p`.`qa_post_id`)AS CNT
    FROM `qa_store` as store
    INNER JOIN `qa_product` as product ON `store`.`qa_store_id` = `product`.`qa_store_id`
    INNER JOIN `qa_post` as p ON `p`.`qa_ref_id` = `product`.`id` AND p.qa_post_type = 'product' AND `p`.`mod_status` = 'valid'";
     if($parent_id != 0)
     {
      $sql.= "AND p.qa_parent_id > 0 ";
     }
     else
     {
       $sql.="AND  `p`.`qa_parent_id` = 0 ";
     }
    $sql .= "INNER JOIN `post_spam` as s ON `s`.`post_id` = `p`.`qa_post_id`
    WHERE `store`.`qa_store_id` = $id";
    
    $res = $this->db->query($sql)->result_array();
    return isset($res[0])?$res[0]['CNT']:0;
  }

  private function search_criteria($search_text, $field = 'p.qa_title')
  {
    $search_text = trim($search_text);
    $search_text = explode(' ', $search_text);

    foreach($search_text as &$text)
    {
      $text = 'CONCAT(space(1),'.$field.',space(1)) LIKE "% '.mysql_escape_string($text).' %"';
    }

    return join(' OR ', $search_text);
  }

  function getPostForProducts ($user_id, $ref_id, $ref_type, $filter = '', $offset = 0, $limit = 5, $search_text = '')
  {
    if(!trim($user_id))
      $user_id = 0;

    if(trim($search_text))
    {
      $search_text = $this->search_criteria($search_text);
    }

    list($order_by, $having, $case) = $this->post_query_filters($filter);

    $query = '
      SELECT p.*, count(a.qa_post_id) as total_answers, v.vote_id, vote_temp.pos_vote, vote_temp.neg_vote, u.name,
        '.$case.'
      FROM qa_post p
      LEFT JOIN qa_post a ON a.qa_parent_id = p.qa_post_id AND a.mod_status = "valid" AND a.mod_level <> 1
      LEFT JOIN post_vote v ON v.post_id = p.qa_post_id AND v.user_id = '.$user_id.'
      LEFT JOIN (
        SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
        FROM post_vote
        GROUP BY post_id
      ) as vote_temp ON vote_temp.post_id = p.qa_post_id
      INNER JOIN qa_user as u ON u.qa_user_id = p.qa_user_id
      WHERE 
        (
          (
            p.qa_ref_id IN
            (
              SELECT prod.id
              FROM qa_product prod
              WHERE prod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
            )
            AND p.qa_post_type = "product"
          )
          OR
          (
            p.qa_ref_id = "'.$ref_id.'"
            AND p.qa_post_type = "'.$ref_type.'"
          )
        )
        AND p.qa_parent_id = 0
        AND p.mod_status = "valid"
        AND p.mod_level <> 1
        '.(trim($search_text) ? ' AND ('.$search_text.')' : '').'
        '.$this->get_recent_tab_filter($filter).'
      GROUP BY p.qa_post_id
      '.(trim($having) ? 'HAVING '.$having : '').'
      '.(trim($order_by) ? 'ORDER BY '.$order_by : '').'
      LIMIT '.$offset.', '.$limit.'
    ';//echo '<pre>'.$query.'</pre>';

    return $this->db->query($query)->result_array();
  }

  /**
   * function postDetailsCount
   *
   * @param <int> $ref_id
   * @param <string> $ref_type
   * @param <string> $filter
   *
   * return count of questions found accroding to custom criteria
   */
  function getPostForProductsCount ($ref_id, $ref_type, $filter = '', $post_id = 0, $search_text = '')
  {
    $filter = strtolower(trim($filter));
    if(!in_array($filter, array('answered', 'unanswered')))
    {
      $query = '
        SELECT count(*) as total_quest
        FROM qa_product prod
        INNER JOIN qa_post p ON prod.id = p.qa_ref_id AND p.qa_post_type = "product"
        WHERE prod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
          AND p.qa_parent_id = '.$post_id.'
          AND p.mod_status = "valid"
          AND p.mod_level <> 1
      ';
    }
    elseif ($filter == 'answered')
    {
      $query = '
        SELECT count(distinct qa_parent_id) as total_quest
        FROM qa_product prod
        INNER JOIN qa_post p ON prod.id = p.qa_ref_id AND p.qa_post_type = "product"
        WHERE prod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
          AND qa_post_type = "' . $ref_type . '"
          AND qa_parent_id > 0
          AND mod_status = "valid"
          AND mod_level <> 1
      ';
    }
    else
    {
      $query = '
        SELECT count(qa_post_id) as total_quest
        FROM qa_product prod
        INNER JOIN qa_post p ON prod.id = p.qa_ref_id AND p.qa_post_type = "product"
        WHERE prod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
          AND qa_parent_id = 0
          AND mod_status = "valid"
          AND mod_level <> 1
          AND qa_post_id NOT IN (
            SELECT distinct(ip.qa_parent_id) as qa_parent_id
            FROM qa_product iprod
            INNER JOIN qa_post ip ON iprod.id = ip.qa_ref_id AND ip.qa_post_type = "product"
            WHERE iprod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
              AND qa_parent_id > 0
              AND mod_status = "valid"
              AND mod_level <> 1
          )
      ';
    }

    if(trim($search_text))
    {
      $search_text = $this->search_criteria($search_text, 'qa_title');

      $query .= (trim($search_text) ? ' AND ('.$search_text.')' : '');
    }
    
    $query .= $this->get_recent_tab_filter($filter);

    $result = $this->db->query($query)->result_array();

    return $result[0]['total_quest'];
  }
  function getReportByPostId($ref_id,$type,$from,$to)
  {
     $query = "SELECT COUNT(*) AS `numrows`, DATE_FORMAT(`qa_created_at`,'%Y-%m-%d')AS Date
          FROM (`qa_post`)
          WHERE `qa_ref_id` = '$ref_id'
         AND `qa_post_type` = '$type'
          AND `qa_created_at` BETWEEN '$from' AND '$to' GROUP BY DATE_FORMAT(`qa_created_at`,'%Y-%m-%d')";
    $result = $this->db->query($query)->result_array();
    return $result;
  }

  function getAnswersCount($post_id)
  {
    $this->db->select('COUNT(*) as cnt');
    $this->db->where('qa_parent_id', $post_id);
    $this->db->where('mod_status', 'valid');
    $this->db->where('mod_level <> 1');

    $res = $this->db->get('qa_post')->result_array();

    return $res[0]['cnt'];
  }

  function deletePostByCategoryId($category_id, $type)
  {
    $this->db->where('qa_post_type',$type);   
    $this->db->where('qa_ref_id',$category_id);
    $this->db->delete('qa_post');
  }
  
  function isAnswerFromWidgetUser($post_id)
  {
    $this->db->select('qa_user_id');
    $this->db->where('qa_post_id', $post_id);
    $this->db->where('qa_parent_id > 0');
    
    $row = $this->db->get('qa_post')->result_array();
    
    return ($row) ? $row[0]['qa_user_id'] : 0;
  }
  
  /**
   * Detect the position of a row in result set
   * 
   * Used for smart routing to a specific question on widgt load
   * 
   * 
   */
  function get_question_position_criteria($post_id, $order_by)
  {
    $query = '
      SELECT vote_temp.pos_vote, vote_temp.neg_vote
      FROM qa_post p
      LEFT JOIN (
        SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
        FROM post_vote
        WHERE post_id = '.$post_id.'
      ) as vote_temp ON vote_temp.post_id = p.qa_post_id
      WHERE p.qa_post_id = ' . $post_id . '
    ';

    $post_info = $this->db->query($query)->result_array();
    
    if($post_info)
    {
      $post_info = $post_info[0];
      
      if(!trim($post_info['pos_vote']))
        $post_info['pos_vote'] = 0;
      
      if(!trim($post_info['neg_vote']))
        $post_info['neg_vote'] = 0;
    }
    
    return $post_info;
  }
  
  function get_question_offset($post_id, $ref_id, $ref_type)
  {
    $offset = 0;
    
    list($order_by, $having, $case) = $this->post_query_filters('');

    $post_info = $this->get_question_position_criteria($post_id, $order_by);
    
    if($post_info)
    {
      $query = '
        SELECT COUNT(*) as cnt
        FROM qa_post p
        LEFT JOIN (
          SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
          FROM post_vote
          GROUP BY post_id
        ) as vote_temp ON vote_temp.post_id = p.qa_post_id
        WHERE vote_temp.pos_vote >= ' . $post_info['pos_vote'] . ' AND
          vote_temp.neg_vote >= ' . $post_info['neg_vote'] . ' AND
          p.qa_ref_id = ' . $ref_id . ' AND
          p.qa_post_type = "' . $ref_type . '" AND
          p.qa_parent_id = 0 AND
          p.mod_status = "valid" AND
          p.mod_level <> 1
        '.(trim($order_by) ? 'ORDER BY '.$order_by : '').'
      ';
      
      $post_info = $this->db->query($query)->result_array();
      
      $offset = $post_info[0]['cnt'] - ($post_info[0]['cnt'] % 5);
    }
    
    return $offset;
  }
  
  /**
   * Detect the position of a row in result set from category or brand, and all its products
   * 
   * Used for smart routing to a specific question on widgt load
   * 
   * 
   */
  function get_question_offset_for_category ($post_id, $ref_id, $ref_type, $limit = 2)
  {
    $offset = 0;
    
    list($order_by, $having, $case) = $this->post_query_filters('');

    $post_info = $this->get_question_position_criteria($post_id, $order_by);
    
    if($post_info)
    {
      $query = '
        SELECT COUNT(*) as cnt
        FROM qa_post p
        LEFT JOIN (
          SELECT post_id, SUM(pos_vote) as pos_vote, SUM(neg_vote) as neg_vote
          FROM post_vote
          GROUP BY post_id
        ) as vote_temp ON vote_temp.post_id = p.qa_post_id
        WHERE
          vote_temp.pos_vote >= ' . $post_info['pos_vote'] . ' AND
          vote_temp.neg_vote >= ' . $post_info['neg_vote'] . ' AND
          (
            (
              p.qa_ref_id IN
              (
                SELECT prod.id
                FROM qa_product prod
                WHERE prod.'.($ref_type == 'category' ? 'qa_category_id' : 'qa_brand_id').' = ' . $ref_id . '
              )
              AND p.qa_post_type = "product"
            )
            OR
            (
              p.qa_ref_id = "'.$ref_id.'"
              AND p.qa_post_type = "'.$ref_type.'"
            )
          )
          AND p.qa_parent_id = 0
          AND p.mod_status = "valid"
          AND p.mod_level <> 1
        '.(trim($order_by) ? 'ORDER BY '.$order_by : '').'
      ';
      
      $post_info = $this->db->query($query)->result_array();
      
      $offset = $post_info[0]['cnt'] - ($post_info[0]['cnt'] % $limit);
    }

    return $offset;
  }

  function count_unmoderated_question($store_id)
  {
    $query = 'SELECT count(*) as cnt, CASE WHEN qa_parent_id > 0 THEN "answer" ELSE "question" END AS post_type
      FROM qa_post
      WHERE qa_store_id = '.$store_id.' AND
        (mod_status IS NULL OR (mod_status = "valid" AND mod_level = 1))
      GROUP BY post_type
    ';
    
    $rows = $this->db->query($query)->result_array();
    
    $data = array(
      'answer'   => 0,
      'question' => 0
    );
    
    foreach($rows as $row)
    {
      $data[$row['post_type']] = $row['cnt'];
    }
    
    return $data;
  }

  function get_per_month_question_volume($store_id)
  {
    $query = 'SELECT count(*) as cnt, DATE_FORMAT(qa_created_at, "%Y-%m") as created_at
      FROM qa_post
      WHERE qa_store_id = '.$store_id.'
      GROUP BY created_at
    ';
    
    $rows = $this->db->query($query)->result_array();
    
    $data = array();
    
    foreach($rows as $row)
    {
      $data[date('M Y', strtotime($row['created_at']))] = intval($row['cnt']);
    }
    
    return $data;
  }
  
  /**
   * 
   * function suggest
   * 
   * @param <int> $store_id
   * @param <string> $search_term
   * 
   */
  function suggest($store_id, $search_term)
  {
    $this->db->where('qa_parent_id', 0);
    $this->db->where('qa_store_id', $store_id);
    $this->db->where('qa_title like "'.mysql_escape_string($search_term).'%"');
    
    return $this->db->get('qa_post')->result_array();
  }
  
  /**
   * 
   * function similarPosts
   *
   * @param <int>  $ref_id
   * @param <string>  $ref_type
   * @param <int>  $question_id
   * 
   * 
   */
  function similarPosts($ref_id, $ref_type, $question_id)
  {
    $this->db->where('qa_parent_id', 0);
    $this->db->where('qa_ref_id', $ref_id);
    $this->db->where('qa_post_type', $ref_type);
    $this->db->where('qa_post_id <> '.$question_id);
    
    $this->db->limit(10);
    
    $rows = $this->db->get('qa_post')->result_array();
    
    if(empty($rows) && $ref_type == 'product')
    {
      $CI =& get_instance();
      $product = $CI->product->getInfo($ref_id);
      
      if($product && ($product['qa_category_id'] > 0 || $product['qa_brand_id'] > 0))
      {
        $where = array();
        if($product['qa_category_id'] > 0)
          $where[] = '(qa_ref_id = '.$product['qa_category_id'].' AND qa_post_type = "category")';
        
        if($product['qa_brand_id'] > 0)
          $where[] = '(qa_ref_id = '.$product['qa_brand_id'].' AND qa_post_type = "brand")';
        
        $where = '('.join(' OR ', $where).')';
        
        $this->db->where('qa_parent_id', 0);
        $this->db->where('qa_post_id <> '.$question_id);
        $this->db->where($where);

        $this->db->limit(10);

        $rows = $this->db->get('qa_post')->result_array();
      }
    }
    
    return $rows;
  }
  
  /**
   * 
   * function changeModStatus
   * 
   * @param <int>      $store_id
   * @param <array>    $questions
   * @param <string>   $status
   * @param <string>   $user_name
   * @param <string>   $action_text
   * 
   */
  function changeModStatus($store_id, $questions, $status, $user_name, $action_text)
  {
    $this->db->where('qa_store_id', $store_id);
    $this->db->where_in('qa_post_id', $questions);
    
    $this->db->update('qa_post', array('mod_status'  =>  $status));
    
    foreach($questions as $question)
    {
      Post_history::saveHistory($question, 'moderate', $user_name, false, ucfirst($action_text));
    }
  }
  
    
  /**
   * 
   * function getContributorDetail
   * 
   * @param <int>       $ref_id
   * @param <string>    $ref_type
   * @param <int>       $contributor_id
   * 
   * 
   */
  function getContributorDetail($ref_id, $ref_type, $contributor_id)
  {
    $this->db->where('qa_ref_id', $ref_id);
    $this->db->where('qa_post_type', $ref_type);
    $this->db->where('qa_user_id', $contributor_id);
    
    $rows = $this->db->get('qa_post')->result_array();
    
    $result = array();
    
    foreach($rows as $row)
    {
      if($row['qa_parent_id'] > 0)
      {
        $result['answers'][] = $row;
      }
      else
      {
        $result['questions'][] = $row;
      }
    }
    
    return $result;
  }
}
