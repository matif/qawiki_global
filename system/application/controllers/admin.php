<?php


class admin extends Controller
{
  function admin()
  {
    parent::Controller();

    $this->load->model('qa_login');
    $this->load->model('email_templates');

    $this->uid = $this->session->userdata("uid");
    if($this->session->userdata("is_admin") != 1)
    {
      redirect('register');
    }

    $this->layout = 'admin';
  }

 /**
  * Email template management
  *
  */
  function index($offset = 0)
  {
    $data['templates'] = $this->email_templates->get_all($offset, 0);

    $types = array_flip($this->config->item('email_template_type'));
    foreach($data['templates'] as $key => $template)
    {
      unset($types[$template['type']]);
    }

    $data['template_type'] = array_keys($types);

    $this->load->view('admin/emailTemplates', $data);
  }

  function saveTemplate()
  {
    $contents = $this->input->post('content');
    $email_type = $this->input->post('email_type');
    $template_id = $this->input->post('template_id');

    if($template_id < 1)
    {
      $this->email_templates->save($contents, $email_type);
    }
    else
    {
      $this->email_templates->update($template_id, $contents, $email_type);
    }

    exit('1');
  }

  function deleteTemplate()
  {
    $template_id = $this->input->post('template_id');

    if(trim($template_id))
    {
      $this->email_templates->delete($template_id);
    }

    exit('1');
  }

 /**
  * Staff management
  *
  */
  function staff($offset = 0)
  {
    $data['users'] = $this->qa_login->getUsersByType();

    $this->load->view('admin/staff', $data);
  }

  function saveStaff()
  {
    $user_id = trim($this->input->post('user_id'));
    $name = trim($this->input->post('user_name'));
    $email = trim($this->input->post('user_email'));
    $password = trim($this->input->post('user_password'));

    $data = array(
      'name'     => $name,
      'email'    => $email,
      'password' => $password,
      'type'     => 'admin',
    );

    if($this->qa_login->check_email_exist($email, $user_id))
    {
      die('email');
    }

    if($user_id < 1)
    {
      $data['created'] = date('Y-m-d H:i:s');
      $this->qa_login->login($data);
    }
    else
    {
      $this->qa_login->update_staff($user_id, $data);
    }

    exit('1');
  }

  function deleteStaff()
  {
    $user_id = $this->input->post('user_id');

    if(trim($user_id))
    {
      $this->qa_login->delete($user_id);
    }

    exit('1');
  }
}