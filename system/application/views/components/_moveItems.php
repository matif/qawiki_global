<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div style="width:auto" class="head nopad"><?php echo $title ?></div>
  </div>
  <div class="content_accordian">
    <div class="noborder  pad10 fs14">
      <div class="seller_list">
        <div class="seller_list_head">All <?php echo $sub_heading?> Options</div>
        
        <?php if (isset($picker) && trim($picker)): ?>

          <a href="javascript:;" rel="<?php echo $picker_route ?>" class="item-picker-btn" style="margin: 13px 0 0 30px; display: inline-block">Use <?php echo $sub_heading?> Picker</a>

        <?php endif; ?>
        
        
        <div class="clear"></div>
        <div class="list_cnt move-items">
          
          <ul>
            <?php foreach ($items as $item): ?>

              <li rel="<?php echo $item['id'] ?>"><?php echo $item[$item_field] ?></li>

            <?php endforeach; ?>
          </ul>
          
        </div>
      </div>
      
      <div class="btn_are move-actions">
        <a href="javascript:;" class="button clearfix" rel="add">
          <span class="lft_area"></span>
          <span class="rpt_content btn_fs14">Move &gt;</span>
          <span class="rgt_area"></span>
        </a>
        <a href="javascript:;" class="button clearfix mt10" rel="remove">
          <span class="lft_area"></span>
          <span class="rpt_content btn_fs14">&lt; Move</span>
          <span class="rgt_area"></span>
        </a>
      </div>
      
      <div class="seller_list" rel="<?php echo $post_field ?>">
        <div class="seller_list_head">Selected <?php echo $title ?> for Custom Report</div>
        <div class="clear"></div>
        <div class="list_cnt move-items" <?php echo isset($move_id) ? 'id="' . $move_id . '"' : '' ?>>
          <ul class="act_lst">            

          </ul>
        </div>        
      </div>
      <div class="clear"></div>
    </div>
  </div>

</div>

<?php if (isset($move_id))  unset($move_id); ?>