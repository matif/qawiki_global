<div class="content_accordian">
  <div class="disp_content_white noborder nopad lnk_hover">
    <table cellspacing="0" cellpadding="0" border="0" width="1160" class="rpt_area">
      <tbody>
        <tr>
          <th width="50">&nbsp;</th>
          <th width="150" height="34">Member ID</th>
          <th width="250">Username</th>
          <th width="300">Email Address</th>          
          <th width="200">Join Date</th>
          <th>Action</th>
        </tr>
        <?php foreach ($teammembers as $member): ?>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center"><?php echo $member["qa_team_member_id"] ?> <input type="hidden" value="<?php echo $member["qa_team_id"]; ?>" class="teamId"/> </td>
            <td><?php echo $member["name"] ?></td>
            <td><?php echo $member["email"] ?></td>          
            <td><?php echo $member["qa_created"] ?></td>
            <?php if ($member["role"] != "view" || $this->user_role == "creator") : ?>            
              <td><a href="javascript:;" rel="<?php echo $member["qa_team_id"] ?>:|:<?php echo $member["qa_team_member_id"] ?>:|:<?php echo $this->store_id ?>"class="edit-memeber">Edit</a> 
                |<?php if ($member["role"] != "creator"): ?> 
                  <a href="<?php echo base_url() ?>teammembers/delete/<?php echo $member["qa_team_id"] ?>/<?php echo $member["qa_team_member_id"] ?>">Delete</a> 
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            <?php else: ?>
              <td>-|-|-</td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td></td>
          <?php if ($this->view["role"] != "view"): ?>            
            <td colspan="6" id=""><span id="sign">+</span><a id="add_m" href="javascript:;">Add New Member</a></td>
          <?php else: ?>
            
          <?php endif; ?> 
        </tr>
      </tbody>
    </table>
  </div>
</div>