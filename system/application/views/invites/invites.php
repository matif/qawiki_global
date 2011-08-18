<script type="text/javascript">
  function acceptInvitation(team_id , invite_id)
  {
    $.post(base_url+'teams/accept/'+team_id+'/'+invite_id, function(){
      window.location.reload()
    });
  }
  function rejectInvitation(invite_id)
  {
    $.post(base_url+'teams/reject/'+invite_id, function(){
      window.location.reload()
    });
  }
</script>

<div class="rgt_850">
  <div class="header_rgt">
    <h1>Invites</h1>

    <div class="clear"></div>
  </div>
  <br />
  <br />
  <?php $i = 0; ?>
  <?php foreach ($invites as $invite): ?>
    <?php if ($this->count > 1): ?>
      <div style="padding-top: 5px;"><hr/></div>
    <?php endif; ?>
    <div style="padding-top: 5px;" >
      <strong>Store Name: </strong><?php echo $invite['qa_store_name']; ?>
    </div>
    <div style="padding-top: 5px;">
      <strong>Owner:</strong> <?php echo $user_info[$i]['name']; ?>
    </div>
    <div style="padding-top: 5px;">
      <a href="javascript:;" onclick="acceptInvitation(<?php echo $invite['qa_team_id'] ?>, <?php echo $invite['invite_id'] ?>)"><input type ="button" value ="Accept" id="accept" /></a> &nbsp;
      <a href="javascript:;" onclick="rejectInvitation(<?php echo $invite['invite_id'] ?>)" ><input type ="button" value ="Cancel" id="cancil" /></a>
    </div>

    <?php $i++; ?>
  <?php endforeach; ?>

  <?php if(count($invites) == 0):?>
      No invites found
  <?php endif;?>

</div>
