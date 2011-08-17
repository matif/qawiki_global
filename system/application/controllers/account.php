<?php

class Account extends Controller
{
  private $user_id;

  function Account()
  {
    parent::Controller();

    $this->load->library('session');
    $this->load->library('form_validation');
    $this->load->library('validation');

    $this->load->model('qa_login', "qa_login");
    $this->load->model('team_invites', "invites");
    $this->load->model('stores', "store");

    $this->user_id = $this->session->userdata("uid");
    $this->uid = $this->user_id;

    verify_logged_in_user();
    if(trim($this->uid))
     set_store_list($this->store, $this->uid);

    set_body_class();

    $this->layout = 'new_layout';
  }

  function index()
  {
    $this->data->user_info = $this->qa_login->get_one($this->user_id);
    $this->load->view('account/account', $this->data);
  }

  function edit_setting()
  {
    $rules = array(
      array(
        'field' => 'email',
        'label' => 'Email',
        'rules' => 'trim|required|valid_email'
      )
    );

    $account_info = array();

    $this->form_validation->set_rules($rules);
    $email = $this->input->post('email');
    $check = $this->qa_login->checkEditMailDuplication($email, $this->session->userdata("uid"));
    
    if ($this->input->post("edit_setting_button") && $check != false)
    {
      if ($this->form_validation->run())
      {
        $account_info['email'] = $this->input->post('email');
        
        
        if (isset($account_info['email']))
        {
          $this->qa_login->update_email($this->user_id, $account_info['email']);          
          $this->data->html = "Account Information updated successfully.";
          $this->data->result = "success";
          $this->data->div_class = 'success';
        }
        else
        {
          $this->data->html = "Unable to update Account Information.";
          $this->data->result = "failed";
          $this->data->div_class = 'error';
        }

        echo json_encode($this->data);
        exit();
      }else{
        echo 1;
        exit;
      }
      
    }
    else
    {
     $this->data->html = "The eamil is already regisetered";
     $this->data->result = "success";
     $this->data->div_class = 'error';
     echo json_encode($this->data);
     exit;
    }
  }

  function changepassword()
  {
    if ($this->input->post('change_password'))
    {
      if ($this->input->post('old_password') != '')
      {
        $rules = array(
          array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|matches[cnfm_password]'
          ),
          array(
            'field' => 'cnfm_password',
            'label' => 'Confirm Password',
            'rules' => 'trim|required'
          )
        );

        $old_pass = ($this->input->post('old_password'));

        if ($this->qa_login->check_old_password($old_pass, $this->user_id))
        {
          $this->form_validation->set_rules($rules);

          if ($this->form_validation->run())
          {
            $passwords = array();
            $passwords['password'] = ($this->input->post('password'));

            if ($old_pass == $passwords['password'])
            {
              $this->data->html = "Unable to change password, password is same as previous.";
              $this->data->result = "failed";
              $this->data->div_class = 'error';
            }
            else
            {
              if (strlen($passwords['password']) > 5)
              {
                $this->qa_login->update_password_by_id($this->user_id, $passwords['password']);
                $this->data->html = "Password has been changed successfully.";
                $this->data->result = "success";
                $this->data->div_class = 'success';
              }
              else
              {
                $this->data->html = "Unable to change password.";
                $this->data->result = "failed";
                $this->data->div_class = 'error';
              }
            }

            echo json_encode($this->data);
            exit();

          }
          else
          {
            $this->data->html = "Invalid Characters password.";
            $this->data->result = "failed";
            $this->data->div_class = 'error';

            echo json_encode($this->data);
            exit();
          }
        }
        else
        {
          $this->data->html = "Invalid old password.";
          $this->data->result = "failed";
          $this->data->div_class = 'error';

          echo json_encode($this->data);
          exit();
        }
      }
    }
  }

  function checkEmailDuplication()
  {
    //  if($this->input->post('email')) {
      $email = $this->input->post('email');
      $check = $this->qa_login->checkEditMailDuplication($email, $this->session->userdata("uid"));
      ($check == false ? $val = -2 : $val = 0 );
    //}
    echo $val;
    die();
  }

}