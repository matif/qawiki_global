<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of emailTemplates
 *
 * @author purelogics
 */
class emailTemplates extends Controller
{
    //put your code here
  function emailTemplates()
  {
    parent::Controller();
    
    $this->load->model('qa_login');
    $this->load->model('email_templates');
    $this->load->model('stores','store');
	
    
    $this->layout = 'new_layout'; 
    $this->uid = $this->session->userdata('uid');
    
    verify_logged_in_user();
    
    set_store_list($this->store, $this->uid);
  }
  
/**
 *
 * @param <type> $offset 
 */
  
  function index($store_id)
  {    
    $user_id	=	$this->uid;
    $user_role = Permissions::can_edit($store_id, $this->uid);
    
    if($user_role == 'view')
      redirect('post/showProduct/'.$store_id);
    
    $store_data = $this->store->getStoreById($store_id);
    
    $this->store_slot = array(
      'sub_heading' => 'User Templates',
      'store'       => $store_data[0]
    );
    $params = parse_grid_params();
    $this->store_id = $store_id;
    
    $data['templates'] = $this->email_templates->get_all($params['grid_offset'],$store_id,null,10, $params['grid_column'], $params['grid_order']);
    
    $types = array_flip($this->config->item('email_template_type'));
    foreach($data['templates'] as $key => $template)
    {
      unset($types[$template['type']]);
    }
	
	$data["emailtempletes"] = $this->email_templates->getEmailTempletesByUserId($user_id);
  
    $data['template_type'] = array_keys($types);
    $data = array_merge($data, $params);    
    $this->load->view('emailTemplating/emailTemplates', $data);
  }
  
  

/**
 * saveTempate
 */
  function saveTemplate($store_id)
  {   
    $contents = html_entity_decode($this->input->post('content'));
	
    echo $email_type = $this->input->post('email_type');
    echo $template_id = $this->input->post('template_id');
   
    if($template_id < 1)
    {
      $this->email_templates->save($contents, $email_type,$store_id,$this->uid);      
    }
    else
    {
      $this->email_templates->update($template_id, $contents, $email_type);
    }

    exit('1');
  }
  
/**
 * delete templates
 */
  
  function deleteTemplate()
  {
    $template_id = $this->input->post('template_id');

    if(trim($template_id))
    {
      $this->email_templates->delete($template_id);
    }

    exit('1');
  }
  function edit($template_id)
  {
     $data = $this->email_templates->get($template_id);
     echo json_encode($data[0]);
     exit;
  }
  
   
  function list_email($store_id,$offset = 0)
  {
    $params = parse_grid_params();
    $data= $this->email_templates->get_all($params['grid_offset'],$store_id,null,$params['grid_limit'],$params['grid_column'], $params['grid_order']);
        
     $additional = array(
      '<a rel="{id}" href="javascript:;" class="edit-record">Edit</a> |
        <a rel="{id}" href="javascript:;" class="delete">Delete</a>',      
      );
     
    $columns = array('id', 'content', 'type');    
    $data = format_grid_data($data, count($data), $params['grid_page'], $params['grid_limit'],'id', $additional, $columns);
    render_json_response($data);
  }

  function user_templates($store_id)
  {
    $this->store_id = $store_id;
    $template_type = null;
    $data_view["template"] = $this->email_templates->getTemplateByStoreId($store_id,$this->uid);
    $this->load->view('emailTemplating/userTemplates', $data_view);
  }
}
?>
