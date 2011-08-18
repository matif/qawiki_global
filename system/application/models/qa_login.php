<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of qa_login
 *
 * @author purelogics
 */
class qa_login extends MY_Model {
  
  /**
   * qa_login
   * 
   */
  
  function qa_login()
  {
	parent::Model();
	$this->table = 'users';
	$this->primary_key = 'qa_user_id';	
  }
  
  /**
   *
   * @param <array> $data 
   * login
   */
  
  function login($data)
  {
     $this->db->insert('users', $data);
  }
  
  /**
   *
   * @param <string> $email
   * @param <string> $password
   * @return array
   * checkLogin 
   */
  
  function checkLogin($email,$password)
  {
    $sqlQuery="SELECT * FROM `users` WHERE `email`='$email' AND `password` = '$password'";
    $result=$this->db->query($sqlQuery)->result_array();    
    return $result;
  }
  
  /**
   *check_email_exist
   * @param type $email
   * @param type $user_id
   * @return type 
   */
  
  function check_email_exist($email, $user_id = 0)
  {
    $this->db->where('email', $email);
    if(trim($user_id))
    {
      $this->db->where('qa_user_id <> '.$user_id);
    }

    return $this->db->get('users')->result_array();
  }
  
  /**
   * check_old_password
   * @param type $password
   * @param type $id
   * @return type 
   */
  
  function check_old_password($password, $id)
  {
	$data = array('password'=>$password, 'qa_user_id'=>$id);
	if($this->count_by($data) > 0)
		return true;
	else
		return false;
  }
  
  /**
   * upadate_password_by_id
   * @param <int> $id
   * @param <string> $input 
   */
  
  function update_password_by_id($id, $input)
  {
    $this->db->set('password' , $input);
    $this->db->where('qa_user_id', $id);
    $this->db->update('users');
  }
  
  /**
   * update_password
   * @param <string> $email
   * @param <string> $pwd 
   */
	
  function update_password($email,$pwd)
  {
    $this->db->set('password', $pwd);
    $this->db->where('email', $email);
    $this->db->update('users');
  }
  
  /**
   * update_email
   * @param <int> $id
   * @param <string> $email 
   */
  
  function update_email($id, $email)
  {
    $this->db->where('qa_user_id', $id);
    $this->db->set('email', $email);
    $this->db->update('users');
  }

  /**
   * get_one
   * @param <int> $id
   * @return type 
   */
  
  function get_one($id)
  {
    $this->db->select('users.*');

    if(isset($id)){	    	
            $this->db->where('users.qa_user_id', $id);
    }

    return $this->db->get('users')->result();
 }
 
 /**
  * getUsers
  * @return type 
  */
 
 function getUsers()
 {
   $sqlQuery = "SELECT * FROM users";
   return $this->db->query($sqlQuery)->result();
 }
 
 /**
  *  checkNameDuplication
  * @param <string> $name
  * @return type 
  */
 
 function checkNameDuplication($name)
 {
    $sqlQuery= "SELECT * FROM users WHERE `name` = '$name'";
    $result = $this->db->query($sqlQuery)->result();
    if($result != NULL)
    {
      return false;
    }
    else
    {
      return true;
    }
 }
 function checkEmailDuplication($email) 
 {
    $sqlQuery= "SELECT * FROM users WHERE `email` = '$email' AND `type` != 'widget'";
    $result = $this->db->query($sqlQuery)->result();    
    if($result != NULL)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  function checkEditMailDuplication($email, $user_id)
  {
    $sqlQuery = "SELECT * FROM users WHERE `email` = '$email' AND `qa_user_id` != '$user_id' AND `type` != 'widget'";
    $result = $this->db->query($sqlQuery)->result();    
    if($result != NULL)
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  function getUserByName($userEmail , $user_id) {
    $this->db->select('email');
    $this->db->select('qa_user_id');
    $this->db->where('qa_user_id !='.$user_id );
    $this->db->where('type','customer' );
    $this->db->like('email' , "$userEmail");
    return $this->db->get('users')->result_array();
  }
 function getUserById($uid)
 {
    $this->db->select('*');
    $this->db->where('qa_user_id' , "$uid");
    return $this->db->get('users')->result_array();   
 }
 function getUserIdByEmail($userEmail)
 {
   $this->db->select('qa_user_id');
   $this->db->where('email',$userEmail);
   $this->db->where("type","customer");
   $data = $this->db->get('users')->result_array();
   return $data;
 }

  function getWidgetUserById($widget_user_id, $widget_user_email, $url, $nick_name = '')
  {
    $this->db->select('qa_user_id as user_id, name as user_name');
    $this->db->where('widget_referer', $url);
    $this->db->where('widget_user_id', $widget_user_id);

    $users = $this->db->get('users')->result_array();
    if(!$users)
    {
      $this->db->insert('users', array(
        'name'           =>  $nick_name,
        'created'        =>  gmdate('Y-m-d H:i:s'),
        'email'          =>  $widget_user_email,
        'type'           =>  'widget',
        'widget_referer' =>  $url,
        'widget_user_id' =>  $widget_user_id
      ));

      return array(
        'user_id'    => $this->db->insert_id(),
        'user_name'  => $nick_name
      );
    }

    return $users[0];
  }

  function update_widget_user($user_id, $nickname, $email = '')
  {
    $this->db->set('name', $nickname);
    if(trim($email))
    {
      $this->db->set('email', $email);
    }
    
    $this->db->where('qa_user_id', $user_id);
    $this->db->update('users');
  }

  function getUsersByType($type = 'admin')
  {
    $this->db->where('type', $type);

    return $this->db->get('users')->result_array();
  }

  function update_staff($user_id, $data)
  {
    $this->db->where('qa_user_id', $user_id);

    $this->db->update('users', $data);
  }

  function delete($id)
  {
    $this->db->query("DELETE FROM users WHERE qa_user_id = ".$id);
  }

  function getUserInfo($user_id)
  {
    $this->db->select('*');
    $this->db->where('qa_user_id',$user_id);
    $result =  $this->db->get('users')->result_array();
    return isset($result[0]) ? $result[0] : NULL;
  }
  
  function updateAnswersCount($user_id)
  {
    $query = "UPDATE users SET total_answers = total_answers + 1 WHERE qa_user_id = ".$user_id;
    
    $this->db->query($query);
  }
  
  function updateThumbsCount($user_id, $thumb = 'up')
  {
    $query = "UPDATE users SET thumbs_".$thumb." = thumbs_".$thumb." + 1 WHERE qa_user_id = ".$user_id;
    
    $this->db->query($query);
  }
  
  function getUserByCode($user_id,$code)
  {
    $this->db->where("qa_user_id", $user_id); 
    $this->db->where("unique_code", $code); 
    return $this->db->get("users")->result_array();    
  }
  
  function update_code($user_id,$data)
  {
    $this->db->where("qa_user_id", $user_id);
    $this->db->update("users", $data);
  }
}
