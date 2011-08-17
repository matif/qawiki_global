<script type="text/javascript">
  $(document).ready(function(e) {
    $("#login").validate({
      rules:
        {
        email:{
          email:true,
          required:true
        },
        password:"required"
      },
      messages:"This field is required"
    });
    $('#email, #password')
    .listenForEnter()
    .bind('pressedEnter', function()
    {
      $("#login").submit();
    });
  });
</script>

<div class="content_dashboard" style="width:375px; margin: auto">
  <div class="heading_section clearfix">
    <div style="width:280px" class="head chan">Sign In</div>
    <div id="display_1" class="accordian_close"><a href="javascript:;"></a></div>
  </div>
  <div class="content_accordian">
    <form action="<?php echo base_url() ?>index.php/register/auth" method="post" id="login" class="form-cont">
      <div class="disp_content_white" id="content_1">
        <div class="row_dat">
          <?php if (isset($this->error) && $this->error == 1): ?>
            <label class="error">User name and password is not correct</label>
          <?php endif; ?>
        </div>
        <div class="row_dat">
          <?php if (isset($this->error) && $this->error == 2): ?>
            <label class="error">You have to confirm your mailing address to get login in to Q&Awiki(Please check Your spam if mail, not found in inbox)</label>
          <?php endif; ?>
        </div>
        <div class="row_dat">
          <div class="lbel">Email Address:</div>
          <div class="lbl_inpuCnt" style="width:200px"><input type="text" name="email" id="email" /></div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel">Password:</div>
          <div class="lbl_inpuCnt"><input type="password" name="password" id="password" /></div>
          <div class="clear"></div>
        </div>
        <div style="padding-left:120px;" class="row_dat">
          <input type="hidden" value="" id="redirect_url" name="redirect_url">
          <a onclick="$('#login').submit();" href="javascript:;" class="button mt10 clearfix">
            <span class="lft_area"></span>
            <span class="rpt_content">Login</span>
            <span class="rgt_area"></span>
          </a>
          <div class="clear"></div>
        </div>
      </div>
    </form>
  </div>
</div>

