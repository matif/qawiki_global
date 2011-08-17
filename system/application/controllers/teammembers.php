<?php

/**
 * Description of Post
 *
 * @author purelogics
 */
class Teammembers extends qaController {

  function __construct() {
    parent::__construct();

    $this->load->library('form_validation');
    $this->load->model('qa_login', "qa_login");
    $this->load->model('team_invites', "invite");
    $this->load->model('badges', "badge");
    $this->load->model('qa_teams', "qa_teams");
    $this->load->model('qa_team_members', "qa_team_members");
    $this->load->model('user_designations');
    $this->load->library('validation');
    $this->load->library('pagination');

    $this->user_id = $this->session->userdata("uid");
  }

  /*
    Team Members Management
   */

  function create($team_id=0) {
    $teams = $this->qa_teams->getTeams($this->user_id);
    $users = $this->qa_login->getUsers();

    $data_view = array();
    
    $data_view['teams'] = $teams;
    $data_view['users'] = $users;
    $data_view['team_id'] = $team_id;
    
    $this->load->view('teammembers/add', $data_view);
  }

  function edit($team_id=0, $id = 0, $type='',$store_id = 0)                 
  {
    $this->layout = 'empty';
    $this->team_id = $team_id;
    $this->error = '';
    
    $data_view['data'] = array();
    $this->id = $id;
    
    if ($type == '' || $type == "no_type") 
    {       
      $data_view['data'] = $this->qa_team_members->getTeamMemberById($id);      
      $data_view['image'] = json_decode($data_view['data'][0]->image_url, true);
      $badges_data = $this->badge->getBadges($team_id);      
      $data_view['badges'] = $badges_data;
      
      $data_view['designations'] = $this->user_designations->get_designation_name($store_id);                
      $data_view["designations"] = get_designations($data_view["designations"]);
      
      
      $this->load->view('teams/editTeams', $data_view);
    } 
    else 
    {      
      $this->upload->file_name = '';      
      
      $data = array(         
          'designation' => $this->input->post('designation'),
          'notify_me_on_comment' => ($this->input->post('notify_me_on_comment') == 'on' ? 1 : 0),
          'notify_me_on_vote' => ($this->input->post('notify_me_on_vote') == 'on' ? 1 : 0)
      );
      
      if (isset($_FILES['badge']) && trim($_FILES['badge']['name'])) 
      {
        $basePath = $this->config->item('root_dir') . '/uploads/teams/';

        $upload_path = createDir($team_id, $basePath);

        load_upload_library($upload_path);

        $this->upload->file_name = make_image_file_name($_FILES['badge']['name']);
        // upload logo image

        if (!$this->upload->do_upload('badge')) {
          $this->error = array('error' => $this->upload->display_errors());
        }

        if (!$this->error) {
          $data['image_url'] = $this->upload->file_name;
          $this->load->helper('image');
          resize_image($basePath . $team_id . '/' . $data['image_url'], $basePath . $team_id . '/t-' . $data['image_url'], 50, 50);
        } else {
          $this->error = 1;
          $this->load->view('teams/editTeams', $data_view);
          exit;
        }
        $image_data = array(
            'image_url' => $this->upload->file_name,
            'qa_team_id' => $team_id,
            'qa_user_id' => $this->uid
        );
        $this->badge->addBadges($image_data);
      } else if ($this->input->post('image_url')) {
        $data['image_url'] = json_encode($this->input->post('image_url'));
      }
      $this->qa_team_members->updateTeamMember($data, $id, $team_id);
      $view = $this->qa_team_members->checkCurrentUserRole($this->uid, $team_id);
      $data_send = array(
          'view' => $view,
          'data' => $data
      );
      $data_send = json_encode($data_send);
      echo '<script type="text/javascript">';
      echo 'parent.remove_frame("frame_' . $this->team_id . '");</script>';
    }
  }

  function delete($team_id=0, $id) {
    $team_info = $this->qa_team_members->getTeamMemberById($id);
    if ($team_info) {
      $this->qa_team_members->deleteTeamMember($team_info[0]->qa_team_member_id, $team_info[0]->qa_team_id, $team_info[0]->qa_user_id);
    }
    redirect('teams');
  }

  function index($team_id=0, $store_id = 0, $offset = 0) {
    $data_view = array();
    $data_view['team_params'] = parse_pagination_params();
    
    if ($store_id != 0) 
    {
      $this->user_role = Permissions::can_edit($store_id, $this->uid);
      $store_data = $this->store->getStoreById($store_id);
      
      $this->store_id = $store_id;
      $this->layout = "new_layout";
      
      if ($team_id == 0) {
        $team_settings = $this->session->userdata("team_settings");
        $team_id = $team_settings["qa_team_id"];
      }

      $this->store_slot = array(
        'store' => $store_data[0],
        'sub_links' => get_sub_links('settings'),
        'inner_links' => get_inner_links_array('settings'),
        'selected' => 'settings',
        'inner_selected' => 'Team'
      );
    }
    
    $data = $this->qa_team_members->getTeamMembers($team_id, $data_view['team_params']['offset'], $data_view['team_params']['rec_per_page']);

    $this->count = $this->qa_team_members->getTeamMembersCount($team_id);
    $this->view = $this->qa_team_members->checkCurrentUserRole($this->uid, $team_id);
    
    $data_view['team_params']["total_records"] = $this->count;
    pagination_calculate_pages($data_view['team_params']);
    
    $data_send = array(
        'view' => $this->view,
        'data' => $data
    );

    if ($store_id == 0) {
      echo json_encode($data_send);
      exit;
    }
    
    $data_view['team_id'] = $team_id;
    $data_view['teammembers'] = $data;
    
    $this->view = $this->qa_team_members->checkCurrentUserRole($this->uid, $team_id);
    $this->load->view("teams/teammembers", $data_view);
  }

  function addTeammember($team_id=0) {

    if ($this->input->post('add')) {
      $data = array(
          'qa_team_id' => $this->input->post('teamName'),
          'qa_user_id' => $this->input->post('memberName'),
          'role' => $this->input->post('role'),
          'designation' => $this->input->post('designation'),
          'notify_me_on_comment' => ($this->input->post('notify_me_on_comment') == 'on' ? 1 : 0),
          'notify_me_on_vote' => ($this->input->post('notify_me_on_vote') == 'on' ? 1 : 0)
      );
      $this->qa_team_members->addTeamMember($data);
    } else {
      $qa_team_members_id = $this->input->post('qa_team_members_id');
      $data = array(
          'role' => $this->input->post('role'),
          'designation' => $this->input->post('designation'),
          'notify_me_on_comment' => ($this->input->post('notify_me_on_comment') == 'on' ? 1 : 0),
          'notify_me_on_vote' => ($this->input->post('notify_me_on_vote') == 'on' ? 1 : 0)
      );
      $this->qa_team_members->updateTeamMember($data, $qa_team_members_id);
      echo $data['role'];
      exit;
    }
    redirect('teammembers/index/' . $team_id);
  }

  function webRing($store_id = "", $order_by = "asc", $data_send = "") {
    $this->store_id = $store_id;

    $data_pager['role'] = Permissions::can_edit($store_id, $this->uid);

    $store_data = $this->store->getStoreById($store_id);
    $data_pager['permission'] = store_permissions_mapping($store_data[0]->qa_permission);

    $this->store_slot = array(
        'store' => $store_data[0],
        'sub_links' => get_sub_links('settings'),
        'inner_links' => get_inner_links_array('settings'),
        'selected' => 'settings',
        'inner_selected' => 'Web Ring'
    );

    $data["members"] = $this->qa_team_members->get_web_ring_members($store_id, "", $order_by);
    $data["members_inactive"] = $this->qa_team_members->get_web_ring_members($store_id, "inactive", $order_by);
    $data ["inactives"] = $this->invite->get_inactive_members($store_id, $this->current_store_team_id);
    
    $response = array_merge($data["members_inactive"], $data["inactives"]);
    qaController::sortMultiArray($response, "name", 'string', $order_by);
    $response = array_values($response);
    
    if ($data_send == "json_active") {
      echo json_encode($data["members"]);
      exit;
    } elseif ($data_send == "json_inactive") {
      echo json_encode($response);
      exit;
    }    
    $data["inactives"] = $response;    
    $team_settings = $this->session->userdata("team_settings");
    $this->team_id = $team_settings["qa_team_id"];

    $this->load->view("teams/webRing", $data);
  }

  function updateStatus($store_id) {
    $active = $this->input->post("active");
    $inactive = $this->input->post("inactive");
    $team_id = $this->qa_teams->getTeamId($store_id);
    $data = array(
        "is_active" => 1
    );
    $this->qa_team_members->update_status($team_id, $active, $data);
    echo $this->db->last_query();
    $data = array(
        "is_active" => null
    );

    $this->qa_team_members->update_status($team_id, $inactive, $data);

    exit(1);
  }

  /**
   * 
   * function designations
   * 
   * @param <int>       $store_id
   * 
   */
  function designations($store_id) {
    $this->store_id = $store_id;
    echo $data_view['role'] = Permissions::can_edit($store_id, $this->uid);

    $store_data = $this->store->getStoreById($store_id);

    $this->store_slot = array(
        'store' => $store_data[0],
        'sub_links' => get_sub_links('settings'),
        'inner_links' => get_inner_links_array('settings'),
        'selected' => 'settings',
        'inner_selected' => 'Moderation Groups'
    );

    if ($this->input->post("designation")) {      
      $data = array(                    
          "designation_name" => $this->input->post("designation"),
          "role"             => $this->input->post("role"),
          "store_id"         => $store_id
      );

      $this->user_designations->save($data);

      $this->msg = "The designation is createed Successfully";
    }

    $data_view['designations'] = $this->user_designations->get($store_id);
    
    $this->load->view("teams/designation", $data_view);
  }

  /**
   * 
   * function checkDesignation
   * 
   * @param <int>       $store_id
   * @param <string>       $designation
   * 
   */
  function checkDesignation($store_id, $designation) {
    $check = $this->user_designations->already_created($this->user_id, $store_id, $designation);
    if ($check)
      echo -1;
    else
      echo 1;

    exit;
  }

  /**
   * 
   * function deleteDesignation
   * 
   * @param <int>       $store_id
   * @param <int>       $designation_id
   * 
   */
  function deleteDesignation($store_id, $designation_id) {
    Permissions::can_edit($store_id, $this->uid);

    $this->user_designations->delete($store_id, $designation_id);

    exit('1');
  }

  /**
   * 
   * function getDesignationInfo
   * 
   * @param <int>       $store_id
   * @param <int>       $designation_id
   * 
   */
  function getDesignationInfo($store_id, $designation_id) {
    $this->no_layout = true;

    Permissions::can_edit($store_id, $this->uid);

    $designation = $this->user_designations->getById($store_id, $designation_id);

    $this->load->view('teams/_designationRow', array('designation' => $designation));
  }

  /**
   * 
   * function saveEditDesignation
   * 
   * @param <int>       $store_id
   * @param <int>       $designation_id
   * 
   */
  function saveEditDesignation($store_id, $designation_id) {
    $this->no_layout = true;

    Permissions::can_edit($store_id, $this->uid);

    if ($this->input->post("designation")) {
      $data = array(
          "designation_name" => $this->input->post("designation"),
          "role"             => $this->input->post("role")
      );

      $this->user_designations->update($store_id, $designation_id, $data);
    }

    exit('1');
  }

  function get_more_members($team_id, $store_id) 
  {        
    $params = parse_pagination_params();
    $params['page_element_id'] = 'teamPag';
    $this->store_id = $store_id;
    $this->user_role = Permissions::can_edit($store_id, $this->uid);
    $this->view = $this->qa_team_members->checkCurrentUserRole($this->uid, $team_id);
    
    $members = $this->qa_team_members->getTeamMembers($team_id, $params['offset'], $params['rec_per_page']);
    $params['total_records'] = $this->qa_team_members->getTeamMembersCount($team_id);

    pagination_calculate_pages($params);
    
    $data['data'] = $this->load->view('partials/_teammembers', array("teammembers" => $members), true);
    
    $data['pagination'] = $this->load->view('components/_pagination', $params, true);
    
    render_json_response($data);
  }

}
