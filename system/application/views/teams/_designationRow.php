
<div class="content_accordian">
  <div id="content_1">

    <form action="" method="post">
      <div class="row_dat">
        <div style="display: none" class="message" id="message_store"></div>
      </div>
      <div class="row_dat">
        <div class="lbel">Name:</div>
        <div style="width:auto" class="lbl_inpuCnt">            
          <input type="text" style="margin-right: 10px" value="<?php echo $designation['designation_name']?>" name="designation" id="designation" class="account_med required" />
        </div>          
        <div id="duplicate" class="error" style="display: none">Designation already exists</div>
        <div class="clear"></div>
      </div>
      <div class="row_dat">
        <div class="lbel">Role:</div>
        <div class="lbl_inpuCnt" style="width:auto" name="role">
          <select class="fr wid_160px" name="role">
            <option value="admin">Admin</option>
            <option value="view">View</option>                
          </select>
        </div>
        <div class="clear"></div>
      </div>

      <div class="row_dat">
        <div class="lbel">&nbsp;</div>
        <input type="button" name="edit_seller_info" value="" tabindex="8" class=" btn_save" rel="<?php echo $designation['id']?>" onclick="save_edit_designation(this)" />
        <div class="clear"></div>
      </div>
    </form>
    
  </div>
</div>