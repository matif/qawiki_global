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

<a href="<?php echo base_url()?>index.php/teams"><< Team Members</a>
&nbsp;|&nbsp;
<a href="<?php echo base_url()?>index.php/teammembers/create/<?php echo $team_id;?>">Create A Team Member</a>
<br/>

<table width="100%" border="1px" cellpadding="2" cellspacing="0" class="stores-list"> 
 <tr>
    <th align="left">Team name</th>
    <th align="left">Member name</th>	
    <th align="left">Role</th>		
    <th align="center">Actions</th>
 </tr>
 	<?php
	if($teammembers) {
		foreach($teammembers as $teammember){
		?>
	 <tr>		 
			<td align="left"><?php echo $teammember->team_name;?></td>
			<td align="left"><?php echo $teammember->name;?></td>
			<td align="left"><?php echo $teammember->role;?></td>				
			<td align="center">
                          <a href="<?php echo base_url()?>index.php/teammembers/edit/<?php echo $teammember->qa_team_id;?>/<?php echo $teammember->qa_team_member_id;?>" id="editTeam">Edit </a>|
				<a href="<?php echo base_url()?>index.php/teammembers/delete/<?php echo $teammember->qa_team_id;?>/<?php echo $teammember->qa_team_member_id;?>">Delete</a>
			</td>	
	 </tr>			
		<?	
		}
	} else {	
	  ?>
	   <tr>
	   	<td colspan="4" align="center">No Record Found!</td>
	  </tr>
	  <?
	}	
	?>
</table>


</div>

