<?php

class Team extends Controller {

  private $user_id;

  function Account() {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    parent::Controller();
    $this->load->library('session');
    $this->load->library('form_validation');
    $this->load->model('qa_login', "qa_login");
    $this->load->library('validation');
    $this->user_id = $this->session->userdata("uid");
    $this->layout = 'default';
  }

  function index() {
    $this->data->user_info = $this->qa_login->get_one($this->user_id);
    $this->load->view('account/account', $this->data);
  }

  function edit_setting() {
    $rules = array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email'
        )
    );
    $account_info = array();
    $this->form_validation->set_rules($rules);
    if ($this->input->post("edit_setting_button")) {
      if ($this->form_validation->run()) {
        $account_info['email'] = $this->input->post('email');
        if ($this->qa_login->update($this->user_id, $account_info)) {
          $this->data->html = "Account Information updated successfully.";
          $this->data->result = "success";
          $this->data->div_class = 'success';
        } else {
          $this->data->html = "Unable to update Account Information.";
          $this->data->result = "failed";
          $this->data->div_class = 'error';
        }
        echo json_encode($this->data);
        exit();
      }
    }
  }

  function changepassword() {
    if ($this->input->post('change_password')) {
      if ($this->input->post('old_password') != '') {
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
        if ($this->qa_login->check_old_password($old_pass, $this->user_id)) {
          $this->form_validation->set_rules($rules);
          if ($this->form_validation->run()) {
            $passwords = array();
            $passwords['password'] = ($this->input->post('password'));
            if ($old_pass == $passwords['password']) {
              $this->data->html = "Unable to change password, password is same as previous.";
              $this->data->result = "failed";
              $this->data->div_class = 'error';
            } else {
              if ($this->qa_login->update($this->user_id, $passwords)) {
                $this->data->html = "Password has been changed successfully.";
                $this->data->result = "success";
                $this->data->div_class = 'success';
              } else {
                $this->data->html = "Unable to change password.";
                $this->data->result = "failed";
                $this->data->div_class = 'error';
              }
            }
            echo json_encode($this->data);
            exit();
          }
        } else {
          $this->data->html = "Invalid old password.";
          $this->data->result = "failed";
          $this->data->div_class = 'error';
          echo json_encode($this->data);
          exit();
        }
      }
    }
  }

}