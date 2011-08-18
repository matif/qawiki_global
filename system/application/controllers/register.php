<?php

/*
* This class perofrom sign and signUp for the QAwikiwebsite
*/
class Register extends Controller
{
  private $rules = array(
    array(
      'field' => 'first_name',
      'label' => 'First Name',
      'rules' => 'trim|required|alpha_dash_space'
    ),
    array(
      'field' => 'last_name',
      'label' => 'Last Name',
      'rules' => 'trim|required|alpha_dash_space'
    ),
    array(
      'field' => 'username',
      'label' => 'User Name',
      'rules' => 'trim|required'
    ),
    array(
      'field' => 'password',
      'label' => 'Password',
      'rules' => 'trim|required|min_length[5]'
    ),
    array(
      'field' => 'email',
      'label' => 'Email',
      'rules' => 'trim|required|valid_email'
    )
 );
  
 function Register()
 {
    parent::Controller();
    $this->load->library('session');
    $this->load->library('form_validation');
    $this->load->library('validation');
    $this->load->library('validation');
    $this->load->model('qa_login','login');
    $this->load->model('team_invites','invites');
    $this->load->model('qa_team_members',"qa_team_members");
    $this->load->plugin('captcha');
    $this->load->helper('email');

    require(APPPATH.'libraries/recaptchalib.php');

    if($this->session->userdata('uid') && $this->uri->segment(2) != 'logout')
    {
      redirect(base_url().'dashboard');
    }

    $this->uid = $this->session->userdata("uid");
    
    $this->current_action = strtolower($this->uri->segment(2));
  }
  
  /**
*
* function index
*
* used for signin
*
*/
  function index()
  {
    $this->layout = 'public';
    
    $this->load->view('signin');
  }
  
/**
* function signup
* @param <stirng>  code
*
*/
  function signup($code = '')
  {
    $this->layout = 'public';
    
    
    $privatekey = $this->config->item('private_key');
    
    if($code != '')
    {
      $this->session->set_userdata('code', $code);
    }
    
    if(trim($this->input->post('name'))!='' && trim($this->input->post('password'))!='' && trim($this->input->post('email'))!='' && trim($this->input->post('password'))==trim($this->input->post('confirmPassword')))
    {
      $resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$this->input->post("recaptcha_challenge_field"),$this->input->post("recaptcha_response_field"));
      
      if (!$resp->is_valid || trim($this->input->post("recaptcha_response_field"))=='')
      {
        $this->data->html = 'Captcha is incorrect please correct it.';
        $this->data->div_class = 'error';
        $this->data->captcha = create_captcha($data = '', $img_path = './captcha/', $img_url = base_url() . 'captcha/');
        $this->load->view('signUp' , $this->data);
      }
      else
      {        
         $sign_up_code = md5(uniqid().time());
         $data_array = array(
          'name'        => $this->input->post('name'),
          'password'    => $this->input->post('password'),
          'email'       => $this->input->post('email'),
          'type'        => 'customer',
          'unique_code' => $sign_up_code,
          'created'     => gmdate('Y-m-d')
        );
         
        $this->login->login($data_array);
        $insert_id = $this->db->insert_id();        
//        $this->session->set_userdata('uid',$insert_id);
//        $this->session->set_userdata('email',$this->input->post('email'));
//        $this->session->set_userdata('name',$this->input->post('name'));
        
        if(trim($this->session->userdata('code'))!='')
        {
          $invitation = $this->invites->getInviteByCode($this->session->userdata('code'));
          
          if($invitation != NULL)
          {
             $data = array(
              'qa_team_id'           => $invitation['qa_team_id'],
              'qa_user_id'           => $this->db->insert_id(),
              'role'                 => 'view',
              'notify_me_on_comment' => 0,
              'notify_me_on_vote'    => 0,
              'unique_code'          => null
            );
             
            $this->qa_team_members->addTeamMember($data);
            $this->invites->deleteInvitationByCode($this->session->userdata('code'));
          }
        }
        
        $to = $this->input->post('email');
        $from = "support@qawiki.com";
        $subject = "New User Registration";
        
        $message = "      Dear ". $this->input->post('name').", \n ";
        $message .= "      Welcome to Q&A wiki, \n You can verify your account by click on the link given below: \n \n";
        $message .= "      ".base_url()."/register/confirmation/".$sign_up_code."/".$insert_id ." \n \n"; 
        $message .= "        Username:". $this->input->post('name');
        $message .= " \n     Password:". $this->input->post('password')."\n";

        $message .= " \n     Please feel free to contact us at any time should you have questions on how to use our service. \n \n \n";
        $message .= " Regards,\n Q&A wiki Team";
        $message .= " \n Questions? Email us at support@qawiki.com";
        
        $headers = 'From: support@qawiki.com' . "\r\n" .
          'Reply-To:support@qawiki.com' . "\r\n";
        mail($to,$subject,$message,$headers);
        redirect(base_url().'dashboard');
      }
     
    }
    else
    {
      $this->load->view('signUp');
    }
  }
  
  function auth()
  {
    $this->layout = 'public';
    
    if(trim($this->input->post('password'))!='' && trim($this->input->post('email'))!='')
    {
      $email=$this->input->post('email');
      $password=$this->input->post('password');
      $auth=$this->login->checkLogin($email,$password);
      if(isset($auth[0]) && $auth[0]!=NULL)
      { 
        if($auth[0]["unique_code"] != NULL)
        {
          $this->error = 2;
          $this->load->view('signin');
          
        }
        else
        {
          $this->session->set_userdata('uid',$auth[0]["qa_user_id"]);
          $this->session->set_userdata('email',$this->input->post('email'));
          $this->session->set_userdata('name',$auth[0]['name']);
          $this->session->set_userdata('is_admin', ($auth[0]['type'] == 'admin' ? 1 : 0));
          redirect('dashboard/index');
        }

      }
      else
      {
        $this->error = 1;
        $this->load->view('signin');
      }
    }
    else
    {
      echo 'Enter user name and password';
    }
  }
  
  function forgotpassword()
  {
    $this->layout = 'public';
    
    $newpassword = $this->generate_rand_password();
    $email='';
    if((trim($this->input->post('email'))!=''))
      $email = $this->input->post('email');
    else
    {
      $this->load->view('forgetPassword');
    }
    if($this->login->check_email_exist($email) && trim($email))
    {
      $this->load->helper('email');
      $this->login->update_password($email,$newpassword);
      $to = $email;
      $from = "support@QAWiki.com";
      $subject = "Forgot Password";
      $message = "Your password has been changed your new password is '<b>$newpassword</b>'.<br />";
      $message .= "Change your password after login.<br /><br />";
      $message .= "Please feel free to contact us support@QAWiki.com";
      mail($to, $subject, $message, "Content-Type:text/html");
      $this->confirmation = 1;
      $this->load->view('forgetPassword');
    }
    else if(trim($email))
    {
      $this->error = 1;
      $this->load->view('forgetPassword');
    }
    
  }
  
  function generate_rand_password()
  {
    $abc = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $chars = '';
    for($i=1; $i<=8; $i++)
    {
      $chars .= substr($abc, rand(0, strlen($abc)-1), 1);
    }
    return $chars;
  }
  
  
  function confirmation($code = "",$user_id = 0)
  {
    $this->layout = "public";
    if($code != ""  && $user_id != 0)
    {
      $user_info = $this->login->getUserByCode($user_id, $code);

      if(isset($user_info[0]))
      {
         $this->session->set_userdata('uid', $user_info[0]["qa_user_id"]);
         $this->session->set_userdata('email', $user_info[0]['email']);
         $this->session->set_userdata('name', $user_info[0]['name']);       

         $data = array(
             "unique_code" => null
         );       

         $this->login->update_code($user_id, $data);
      }
      $this->load->view('confirmation', $data = array("type" => "confirmation"));      
    }
    else
      $this->load->view('confirmation', $data = array("type" => "signUp"));
  }
  
  function checkDupication()
  {
    $val = 0;
    if($this->input->post('name'))
    {
      $name = $this->input->post('name');
      $check=$this->login->checkNameDuplication($name);
      ($check == false ? $val = -1 : $val = 0 );
    }
    if($this->input->post('email'))
    {
      $email = $this->input->post('email');
      $check=$this->login->checkEmailDuplication($email);
      if($val == -1)
        ($check == false ? $val = -3 : $val = -1 );
      else
        ($check == false ? $val = -2 : $val = 0 );
    }
    echo $val;
    die();
  }
  
  function logout()
  {
    $this->session->unset_userdata('uid');
    $this->session->sess_destroy();    
    redirect('register');
  }
}