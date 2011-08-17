<?php
/* 
 * @package     Permissions
 *
 * @author      Kashif Ali
 */

class Permissions
{
  public static function can_edit($store_id, $user_id, $redirect = true)
  {
    $CI =& get_instance();

    if($CI->session->userdata('is_admin'))
    {
      return 'admin';
    }

    // get store team
    $CI->db->select('qa_team_id');
    $CI->db->where('qa_store_id', $store_id);
    $CI->db->limit(1);
    $result = $CI->db->get('qa_team')->result_array();

    if($result)
    {
      $CI->current_store_team_id = $result[0]['qa_team_id'];      
      
      // team member id
      $CI->db->select('*');
      $CI->db->where('qa_team_id', $result[0]['qa_team_id']);
      $CI->db->where('qa_user_id', $user_id);

      $CI->db->limit(1);
      $result = $CI->db->get('qa_team_member')->result_array();
      
      if($result)
      {
        $CI->session->set_userdata('team_settings', $result[0]);
        $CI->current_store_member_id = $result[0]['qa_team_member_id'];
        
        // member role
        $CI->db->select("role");
        $CI->db->where("id", $CI->current_store_member_id);
        $result = $CI->db->get('moderation_groups')->result_array();
        
        return ($result) ? (!trim($result[0]["role"]) ? "admin" : $result[0]["role"]) : null;
      }
    }

    if($redirect)
    {
      redirect('main/noaccess');
    }

    return false;
  }
}