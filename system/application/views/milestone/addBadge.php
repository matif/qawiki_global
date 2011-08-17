<?php if(!isset($badge_info['milestone_id']) ):?>
<div class="">
  <div class="heading_section  clearfix" >
    <div class="head setting">Add Badges</div>
  </div>  
  <?php endif;?>
  <form method="post" id="badge-form" enctype="multipart/form-data" action="<?php echo base_url() . 'teams/saveMilestoneBadge/' . $store_id . (isset($badge_info['id']) ? '/' . $badge_info['id'] : '') ?>">
   <?php if(!isset($badge_info['milestone_id']) ):?>
    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">
    <?php endif;?>
        <div>
          <label>Badge Name:</label> <input type="text" name="badge_name" id="badge_name" class="required" value="<?php echo isset($badge_info['badge_name']) ? $badge_info['badge_name'] : '' ?>" />
        </div>

        <div>
          <label>Number Awarded:</label> <input type="text" name="number_awarded" id="number_awarded" class="required number" value="<?php echo isset($badge_info['numbers_awarded']) ? $badge_info['numbers_awarded'] : '' ?>" />
        </div>
        <?php if($count > 0 ):?>
        <div>
          <label>Milestone:</label> 
          <?php if ($this->check == 1): ?>
            <input type="text" value="<?php echo $badge_info["badge_name"] ?>" name="milestone"/>
          <? else: ?>
            <?php echo select_tag('milestone', $milestones, (isset($badge_info['milestone_id']) ? $badge_info['milestone_id'] : ''), array(), array(), $diables_badges) ?>
          <?php endif; ?>
        </div>
        <?php else:?>
          <div class="error">No Milestone is created, Please create a milestone before adding any Badge</div>
        <?php endif;?>
        <div>
          <label>Badge Image:</label> <input type="file" name="badge_image" id="badge_image" value="" class="required" />    
          <input type="hidden" value="<?php echo (isset($badge_info['badge_image'])) ? $badge_info['badge_image'] : "" ?>" name="badge_edit"/>
          <?php if (isset($badge_info['badge_image'])): ?>    
            <img src="<?php echo base_url() . 'uploads/' . $store_id . '/custom_badges/t-' . $badge_info['badge_image'] ?>" align="top" />    
          <?php endif; ?>    
          <br>
          <label>&nbsp;</label> Badge image will be resized to 48x48 pixels
        </div>
        <div>
          <label>&nbsp;</label>
          <input class=" btn_save" <?php echo ($count == 0)?'disabled="true"':''?> type="submit" name="edit_seller_info" value="" tabindex="8" />
<!--          <input type="submit" value="save" />-->
        </div>
      </div>
      <?php if(!isset($badge_info['milestone_id']) ):?>
      </div>
      <?php endif;?>
  </form>
  <?php if(!isset($badge_info['milestone_id']) ):?>
  </div>
  <?php endif;?>