<script src="<?php echo javascriptUrl(); ?>validation.js" type="text/javascript" charset="utf-8"></script>
<style type="text/css">
  form.constrain label {
    min-width: 155px;
}
</style>

<?php echo use_javascript('custom')?>
<?php echo link_tag('vtip')?>

<script type="text/javascript">
  $(document).ready(function()
  {
    $(function(){$("a[title]").tooltip();});  
    $("#new_form").validate();
    $("#formID_2").validate();
    $('#username, #email')
    .listenForEnter()
    .bind('pressedEnter', function()
    {
      save_account_info($("#new_form").form);
    });
    
    $('#old_password, #password, #cnfm_password')
    .listenForEnter()
    .bind('pressedEnter', function()
    {      
      alert(1);
      save_changed_password_info($("#new_form").form);
    });
  });
</script>

<div class="home_heading clearfix">
  <div class="heading">My Account</div>
</div>

<form action="" method="post" id="new_form" class="constrain">
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head info">Account Information</div>
      <div class="accordian_close"><a href="javascript:;"></a></div>
    </div>
    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">
        <div class="row_dat">
          <div class="error" id="message_account" style="display: none"></div>
          <div class="error" id="duplicate_email" style="display: none">This Email address is already registered</div>
          <div class ="error" id ="invalid_email" style="display: none">Please Enter Valid Email address</div>
        </div>
        <label>Account Created:&nbsp;&nbsp;&nbsp;<?php $date = explode(" ", $user_info[0]->created); echo $date[0]; ?></label>
        <div class="row_dat">
          <div class="lbel">User Name:</div>
          <div class="lbl_inpuCnt" >
            <input type="text" tabindex='1' name="username" id="username" class="account_med required" value="<?php echo $user_info[0]->name; ?>" disabled="true"  />
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel">Email Address:</div>
          <div class="lbl_inpuCnt" style="width:160px">
            <input type="text" tabindex='2' name="email" id="email" class="account_med email" value="<?php echo $user_info[0]->email; ?>"  />
            
          </div>
          <div class="hlp_ares"><a class="clickTip exampleTip" title="Enter Valid Email address to change Email address" href="javascript:void(0);"><img height="16" width="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <input type="button" class="btn_save" value="" tabindex='4' name="edit_setting_button" id="edit_setting_button" onclick="javascript:save_account_info(this.form);" />
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </div>
</form>

<form action="" id="formID_2" class="constrain" method="post">
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head chan">Change Password</div>
      <div class="accordian_close"><a href="javascript:;"></a></div>
    </div>
    <div class="content_accordian">
      <div class="disp_content_white">
        <div class="row_dat">
          <div class="message" id="message_password" style="display: none"></div>
        </div>
        <div class="row_dat">
          <div class="lbel width155">Current Password:</div>
          <div class="lbl_inpuCnt">
            <input type="password" class="account_med required" name="old_password" id="old_password"  />
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel width155">New Password:</div>
          <div class="lbl_inpuCnt">
            <input type="password" class="account_med required" id="password" name="password"   />
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel width155">Confirm New Password:</div>
          <div class="lbl_inpuCnt">
            <input type="password" name="cnfm_password required" id="cnfm_password" class="account_med" />
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <input type="button" class="btn_save" value="" onclick="javascript:return save_changed_password_info(this.form);" id="change_password" name="change_password" />
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </div>
</form>