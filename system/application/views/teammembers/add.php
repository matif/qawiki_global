<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>


<div class="rgt_850">

<div class="header_rgt">
	<h1>Team Management</h1>
	<div class="clear"></div>
</div>

<a href="<?php echo base_url()?>index.php/teammembers/index/<?php echo $team_id;?>"><< Back to teams members</a>
<br/>

<form action="<?php echo base_url()?>index.php/teammembers/addTeammember/<?php echo $team_id;?>" method="post" id="teamMemFrm">
  
  <div>
    <label>Team Name:</label> 
		<select name="teamName">
			<option>select</option>
			<?php
			foreach($teams as $team){
			  $selected = '';
			  if( isset($team_member_info[0]) && ($team_member_info[0]->qa_teams_id == $team->qa_team_id) )
				  $selected = 'selected="selected"';
			  elseif( $team_id == $team->qa_team_id )
			    $selected = 'selected="selected"';
			  echo '<option '.$selected.' value="'.$team->qa_team_id.'">'.$team->team_name.'</option>';
			}
			
			?>
		</select>
  </div>
  <div>
    <label>Member</label> 
		<select name="memberName">
			<option>select</option>
			<?php
			foreach($users as $user){
			  $selected = '';
			  if( isset($team_member_info[0]) && ($team_member_info[0]->qa_user_id == $user->qa_user_id) )
				  $selected = 'selected="selected"';
			  echo '<option '.$selected.' value="'.$user->qa_user_id.'">'.$user->name.'</option>';
			}			
			?>
		</select>
  </div>  
  <div>
    <label>Role</label> 
		<select name="role">
			<option>Choose</option>
			<option <?php echo ( isset($team_member_info[0]) && $team_member_info[0]->role=='view' ) ? 'selected="selected"' : '';?> value="view">View</option>
			<option <?php echo ( isset($team_member_info[0]) && $team_member_info[0]->role=='admin' ) ? 'selected="selected"' : '';?> value="admin">Admin</option>			
		</select>
  </div>
  
  <div>
    <label>Notify Me On Comment</label> 
		<input <?php echo ( isset($team_member_info[0]) && $team_member_info[0]->notify_me_on_comment=='1' ) ? 'checked="checked"' : '';?> type="checkbox" name="notify_me_on_comment"/>
  </div>
  
  <div>
    <label>Notify Me On Vote</label> 
		<input <?php echo ( isset($team_member_info[0]) && $team_member_info[0]->notify_me_on_vote=='1' ) ? 'checked="checked"' : '';?> type="checkbox" name="notify_me_on_vote"/>
  </div>  
  
  <div class="submit">
    <input type="submit" value="Save" />
  </div>

<?php
  if(isset($team_member_info[0])){
  ?>
  	<input type="hidden" name="qa_team_members_id" value="<?php echo $team_member_info[0]->qa_team_member_id;?>"/>
  <?
  } else {
  ?>
  	<input type="hidden" name="add" value="1"/>
  <?  
  }
?>  
</form>



</div>

