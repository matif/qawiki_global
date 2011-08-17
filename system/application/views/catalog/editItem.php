
<div style="padding: 10px 20px">

  <form action="" class="form-cont">
  
    <div class="row_dat">
      <div style="display: none" class="message" id="message_store"></div>
    </div>

    <div class="row_dat">
      <div class="lbel">ID:</div>
      <div class="lbl_inpuCnt">
        <input type="text" name="itemRemoteId" class="account_med required" value="<?php echo $itemInfo['qa_'.$item_type.'_id']?>" />
      </div>
      <div class="clear"></div>
    </div>

    <div class="row_dat">
      <div class="lbel">Title:</div>
      <div class="lbl_inpuCnt">
        <input type="text" name="itemTitle" class="account_med required" value="<?php echo ($item_type != 'product') ? $itemInfo['qa_'.$item_type.'_name'] : $itemInfo['qa_product_title']?>" />
      </div>
      <div class="clear"></div>
    </div>
    
    <?php if($item_type == 'product'):?>
    
      <div class="row_dat">
        <div class="lbel">Description:</div>
        <div class="lbl_inpuCnt">
          <textarea name="itemDescription" class="account_med textarea required"><?php echo $itemInfo['qa_product_description']?></textarea>
        </div>
        <div class="clear"></div>
      </div>
   
    <?php endif;?>

    <div class="row_dat">
      <div class="lbel wd165">&nbsp;</div>
      <input type="button" name="edit_seller_info" value="" tabindex="8" class=" btn_save" onclick="save_edit_item(this)" />
      
      <input type="hidden" name="item_id" value="<?php echo $item_id?>" />
      <input type="hidden" name="item_type" value="<?php echo $item_type?>" />
      <div class="clear"></div>
    </div>
    
  </form>
</div>