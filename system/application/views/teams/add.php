<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>


<div class="rgt_850">

<div class="header_rgt">
  <h1 style="float: left">Team Management</h1>
  <a href="<?php echo base_url()?>index.php/teams" style="float:right"><< Back to teams</a>
	<div class="clear"></div>
</div>

<br/>

<form action="<?php echo base_url()?>index.php/teams/addTeam" method="post" id="teamFrm" class="constrain">



  <div>
    <label>Team name</label> <input type="text" id="teamName" name="teamName" value="<?php echo isset($team_info[0]) ? $team_info[0]->team_name : '';?>"/>
  </div>
  
  <div>
    <label>Store</label> 
		<select name="storeName">
			<option>select</option>
			<?php
			foreach($stores as $store){
			  $selected = '';
			  if( isset($team_info[0]) && ($team_info[0]->store_id == $store->store_id) )
				  $selected = 'selected="selected"';
			  echo '<option '.$selected.' value="'.$store->qa_store_id.'">'.$store->qa_store_name.'</option>';
			}
			?>
		</select>
  </div>
  
  <div class="submit">
    <input type="submit" value="Create Team" />
  </div>

<?php
  if(isset($team_info[0])){
  ?>
  	<input type="hidden" name="qa_teams_id" value="<?php echo $team_info[0]->qa_team_id;?>"/>
  <?
  } else {
  ?>
  	<input type="hidden" name="add" value="1"/>
  <?  
  }

?>

  
</form>



</div>

