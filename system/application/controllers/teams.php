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

class Teams extends qaController {
    //put your code here
  function  Teams()
  {
    parent::__construct();
    
    $this->load->library('form_validation');
    
    $this->load->model('qa_login',"qa_login");
    $this->load->model('qa_teams',"qa_teams");
    $this->load->model('qa_team_members',"qa_team_members");
    $this->load->model('team_invites',"invites");
    
    $this->load->model('milestones', 'milestones_m');
    $this->load->model('milestone_badges', 'milestone_badges');
    
    $this->load->library('validation');
    $this->load->library('pagination');
  }

  /** 
   * 
   * function getstoreList
   * 
   *
   */
  function getStoreList($id)
  {
    $team_info = $this->qa_teams->getTeamById($id);
    $data = array();
    $data_view = array();
    $data_view['team_info'] = $team_info;  		
    $data_view['stores'] = $data;
    
    echo json_encode(array($data_view));
    
    exit;
  }
  
  function delete($id)
  {
  	
    $team_info = $this->qa_teams->getTeamById($id);	
    if($team_info){
      $this->qa_teams->deleteTeam($team_info['qa_team_id']);
    }
    
    redirect('teams');	 	 	
  }
  
  function index($offset=0)
  {
    $this->layout = 'new_layout';
    $data = $this->qa_teams->getTeams($this->uid, $offset, 10);
    $this->count = $this->qa_teams->getTeamCount($this->uid);
    
    $data_view = array();
    $data_view['teams'] = $data;    
    $data_view = array_merge($data_view,parse_grid_params());
    
    $this->load->view('teams/list' , $data_view);
  }
  
  function displayIndex()
  {
     $additional = array(
      '<a rel="brand|{id}" href="javascript:;" class="viewAsnwer">View Answer</a>',
      '<a rel="brand|{id}" href="javascript:;" onclick = "viewQuestion({id}, \'brand\', this)" class="view-report">View Question</a>'
      );
    $columns = array('	team_name', 'qa_store_name');
    $params = parse_grid_params();
    $teams = $this->qa_teams->getTeams($this->uid, $offset, 10);
    $this->count = $this->qa_teams->getTeamCount($this->uid);
    $teams = format_grid_data($teams, $count, $params['grid_page'], $params['grid_limit'], 'id', $additional, $columns);
    
    render_json_response($brand_questions);
  }
  
  function addTeam()
  {
    if((trim($this->input->post('storeName'))!='')&&(trim($this->input->post('teamName'))!='') && $this->input->post('add') )
    {
      $data=array(
        'qa_store_id'  => $this->input->post('storeName'),
        'team_name'    => $this->input->post('teamName')
      );

      $this->qa_teams->addTeam($data);
    }
    else 
    {
      $team_id = $this->input->post('qa_team_id');
      $data = array(
        'team_name'    => $this->input->post('teamName')
      );
      
      $this->qa_teams->updateTeam($data, $team_id);
      
      echo $data['team_name'];
      exit;
    }

    redirect('teams');
  }

  function getSuggession($term)
  {
    $data = $this->qa_login->getUserByName($term , $this->session->userdata('uid'));    
    $tempData=array();
    for($i=0; $i < count($data);$i++)
    {
      $tempData[$i] = array(
        'Username' => $data[$i]['email'],
        'Id'       => $data[$i]['qa_user_id']
      );
    }
    if($data != null)
    {
      echo json_encode($tempData);
      exit;
    }

    echo -1;
    exit;
  }

  function sendInvitaion()
  {
    $to = $this->input->post('user_email');
    $teamId = $this->input->post('teamId');

    $user_data = $this->qa_login->getUserById($this->uid);    
    $team_data =$this->qa_teams->getTeamById($teamId);
    $store_data = $this->store->getStoreById($team_data['qa_store_id']);
    $invite_user = $this->qa_login->getUserIdByEmail($to);
    
    if($invite_user && !$this->qa_team_members->is_team_member($invite_user[0]['qa_user_id'], $teamId))
    {
      if($this->invites->already_invited($invite_user[0]['qa_user_id'], $teamId) == NULL)
      {        
        $invite_data = array(
          'qa_team_id'  => $teamId,
          'email'       => $to,
          'unique_code' => md5(uniqid().time()),
          'qa_user_id'  => $invite_user[0]['qa_user_id']
        );

        $this->invites->addInvite($invite_data);        
        exit;
      }
    }
    else if($invite_user == NULL)
    {
      if(!$this->invites->already_invited_email($to, $teamId))
      {
        $code = md5(uniqid().time());
        $invite_data = array(
          'qa_team_id'  => $teamId,
          'email'       => $to,
          'unique_code' => $code
        );
        
        $this->invites->addInvite($invite_data);
        
        $subject = 'Invitation by '.$user_data[0]['name'];
        $message = $user_data[0]['name']." has invited you to join".base_url()."register/signUp/".$code." at Store ".$store_data[0]->qa_store_name;
        
        mail($to, $subject, $message);
        exit;
      }
    }
    
    echo -1;
    exit;
  }  
  
  function invites()
  {
    $uid = $this->session->userdata('uid');
    $data_invites = $this->invites->getInvites($uid);    
    $data['invites']=$data_invites;
    
    $this->count = count($data['invites']);
    
    for($i=0; $i < $this->count; $i++ )
    {
      $data['user_info'][$i] = $this->qa_login->getUserInfo($data_invites[$i]['qa_user_id']);
    }  
    
    $this->load->view('invites/invites',$data);
  }
  
  function accept($team_id , $invite_id)
  {
    $this->invites->deleteInvites($invite_id);
    
    $member_data = array(
      'qa_team_id'  => $team_id,
      'qa_user_id'  => $this->session->userdata('uid'),
      'role'        => 'view'
    );
    
    $this->qa_team_members->addTeamMember($member_data);
    
    exit;
  }
  
  function reject($invite_id)
  {
    $this->invites->deleteInvites($invite_id);
    
    exit;
  }
  
  function uploadBadge($param)
  {
    
  }
  
  /**
   * function milestoneBadges
   * 
   * @param <int> $store_id
   * 
   */
  function milestoneBadges($store_id = 0)
  {
    if(!$store_id)
    {
      redirect('dashboard');
    }   
    
    
    $data_view['role'] = Permissions::can_edit($store_id, $this->uid);
    
    $store_data = $this->store->getStoreById($store_id);
    
    $this->store_slot = array(
      'store'          =>  $store_data[0],
      'sub_links'      =>  get_sub_links('settings'),
      'inner_links'    =>  get_inner_links_array('settings'),
      'selected'       =>  'settings',
      'inner_selected' =>  'Badges'
    );
    
    $data_view['store_id'] = $store_id;
    $data_view['badges'] = $this->milestone_badges->get($store_id);
    
    $this->load->view('milestone/badges', $data_view);
  }
  
  /**
   * function saveMilestoneBadge
   * 
   * @param <int> $store_id
   * 
   */
  function saveMilestoneBadge($store_id = 0, $badge_id = 0)
  {
    $badge_info = null;
    
    if(!$store_id || (!trim($this->input->post('badge_name')) &&!trim($this->input->post('badge_edit'))))
    {
      redirect('dashboard');
    }
    
    Permissions::can_edit($store_id, $this->uid);    
    
    $this->load->helper('image');
    
    $path = $this->config->item('root_dir').'/uploads/'.$store_id.'/custom_badges/';
    load_upload_library($path);

    if (trim($_FILES['badge_image']['name']))
    {
      
      $this->upload->file_name = make_image_file_name($_FILES['badge_image']['name']);

      // upload logo image
      if ($this->upload->do_upload('badge_image'))
      {
        $badge_image = $this->upload->file_name;

        // resize image
        $path = $path.$badge_image;
        resize_image($path, str_replace($badge_image, 't-'.$badge_image, $path), 45, 45);
        
      }
    }
    $badge_url = $this->input->post("badge_edit"); 
    $data = array();
    $data['store_id'] = $store_id;
    $data['badge_name'] = $this->input->post('badge_name');
    $data['numbers_awarded'] = $this->input->post('number_awarded');
    $data['milestone_id'] = $this->input->post('milestone');    
    
     $data['badge_image'] = (isset($badge_image) && $badge_image !="")?$badge_image:$badge_url;
    
    
    if($badge_id > 0)
    {     
        $this->milestone_badges->update($badge_id, $data);    
    }    
    else
    {
      $data['created_at'] = gmdate('Y-m-d H:i:s');
      $this->milestone_badges->save($data);
    }
//    else
//    {
//      // delete image and thumb
//      if(trim($badge_info['badge_image']))
//      {
//        $path = $this->config->item('root_dir').'/uploads/'.$store_id.'/custom_badges/'.$badge_info['badge_image'];
//        if(file_exists($path))
//        {
//          unlink($path);
//        }
//
//        $path = str_replace($badge_info['badge_image'], 't-'.$badge_url, $path);
//        echo $badge_info['badge_image'] ;
//        if(file_exists($path))
//        {
//          unlink($path);
//        }
//      }
//
//      $this->milestone_badges->update($badge_info['id'], $data);
//    }
    
    redirect('teams/milestoneBadges/'.$store_id);
  }
  
  /**
   * function deleteMilestoneBadge
   * 
   * @param <int> $store_id
   * 
   */
  function deleteMilestoneBadge($store_id = 0)
  {
    if(!$store_id)
    {
      exit(0);
    }
    
    Permissions::can_edit($store_id, $this->uid);
    
    $this->load->model('milestone_badges', 'milestone_badges');
    
    $badge_id = $this->input->post('badge_id');
    
    $badge_info = $this->milestone_badges->getOne($badge_id, $store_id);
    
    if($badge_info)
    {
      // delete image and thumb
      if(trim($badge_info['badge_image']))
      {
        $path = $this->config->item('root_dir').'/uploads/'.$store_id.'/custom_badges/'.$badge_info['badge_image'];
        if(file_exists($path))
        {
          unlink($path);
        }
        
        $path = str_replace($badge_info['badge_image'], 't-'.$badge_info['badge_image'], $path);
        if(file_exists($path))
        {
          unlink($path);
        }
      }
      
      $this->milestone_badges->delete($badge_id);
    }
    
    exit(1);
  }
  
  /**
   * function getBadgeInfo
   * 
   * @param <int> $store_id
   * 
   */
  function getMilestoneBadgeInfo($store_id = 0, $badge_id = 0)
  {

    $this->no_layout = true;
    
    Permissions::can_edit($store_id, $this->uid);
    
    
    $this->load->model('milestone_badges', 'milestone_badges');
        
    $data_view['store_id'] = $store_id;
    
    $this->check = 0;
    if($badge_id > 0)
    {
      $data_view['badge_info'] = $this->milestone_badges->getOne($badge_id, $store_id);
      $results = $this->milestones_m->checkPredefinedMilestone($data_view['badge_info']["milestone_id"]);                  
      if(!isset($results[0]))
      {        
        $this->check = 1;
      }
      
    }
    
    if($this->check == 0)
    {
      $data_view['milestones'] = $this->milestones_m->store_milestones( $store_id);
      $data_view['milestones'] = $this->milestones_m->format($data_view['milestones']);
    }
    
    $data_view['diables_badges'] = $this->milestone_badges->getBadgesId($store_id, $badge_id);
    $data_view["count"] = $this->milestones_m->countMileStone($store_id);
    $this->load->view('milestone/addBadge', $data_view);
  }
  
  /***
   * function: milestone
   */
  
  function milestone($store_id = "")
  {
    if($store_id == "")
      redirect (base_url());
    
    $this->store_id =  $store_id;
    
    $data_view["role"] = Permissions::can_edit($store_id, $this->uid);
    $store_data = $this->store->getStoreById($store_id);
    $this->store_slot = array(
        'store' => $store_data[0],
        'sub_links'       =>  get_sub_links('settings'),
        'selected'        =>  'settings',
        'inner_links'     => get_inner_links_array('settings'),
        'inner_selected'  => 'Milestones'
    );

    $data_view["milestones"] = $this->milestones_m->store_milestones($store_id);
    $this->load->view("teams/milestone_list", $data_view);
  }

  /**
   * function getMilestoneInfo
   * @param type $store_id
   * @param type $milestone_id 
   */
  
  function getMilestoneInfo($store_id = 0, $milestone_id = 0) {
    
    if($store_id == 0)
      redirect (base_url());
    
    $this->store_id =  $store_id;
    $data_view = array();
    if ($milestone_id > 0) {
      $this->type = "edit";
      $data_view['milestone_edit'] = $this->milestones_m->getMilestone($milestone_id, $store_id);
    }
    echo $this->load->view('teams/milestone', $data_view, true);
    exit();
  }
  
  /**
   * function saveMilestone
   * @param <int> $store_id
   * @param <string> $type
   * @param <int> $milestone_id 
   */
  
  function saveMilestone($store_id = 0, $type = "", $milestone_id = 0)
  {
    if($store_id == 0)
      redirect (base_url());
    
    $this->store_id =  $store_id;
    if (trim($this->input->post("milestone"))) {
      $data = array(
          "name" => $this->input->post("milestone"),
          "question" => $this->input->post("question"),
          "answer" => $this->input->post("answer"),
          "question_liked" => $this->input->post("question_liked"),
          "answer_liked" => $this->input->post("answer_liked"),          
          "store_id" => $store_id,
          "created_at" => gmdate('Y-m-d H:i:s')
      );
      
      if ($type == "") {
        $this->milestones_m->save($data);
      } else {
        $this->milestones_m->update_milestone($milestone_id, $data);        
        
      }
    }

    redirect("teams/milestone/" . $store_id);
  }
  
  /**
   * function deleteMilestone
   * @param type $store_id
   * @param type $milestone_id 
   */
  
  function deleteMilestone($store_id, $milestone_id)
  {
    $this->milestones_m->deleteMilestone($milestone_id, $data);
    exit(1);
  }
  
  /**
   *checkMilestoneDuplication
   * @param <int> $store_id
   * @param <string> $name 
   * 
   */
  
  function checkMilestoneDuplication($store_id,$name)
  {
    $check = $this->milestones_m->isExists($name, $store_id);    
    
    if($check == true)
    {
      echo "error";
    }
    else
      echo "no error";
    exit;
  }
  
}