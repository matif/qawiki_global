<div class="content_dashboard" style="width: 375px; margin: auto">
  <div class="heading_section clearfix">
    <div style="width:280px" class="head chan">Forget Password</div>
    <div id="display_1" class="accordian_close"><a onclick="show_divs('content_1', 'display_1', 'accordian_close', 'accordian_open');" href="javascript:void(0);"></a></div>
  </div>
  <div class="content_accordian">
    <form action="" method="post" class="form-cont" id="forgotFrom">
      <div class="disp_content_white" id="content_1">
        <div class="row_dat">
          <?php if (isset($this->error)): ?>
            <div class="error">Email does not exists </div>
          <?php endif; ?>
          <?php if (isset($this->confirmation)): ?>
            <div class="confirmation-box" style="color:green">Email has been sent to you. </div>
          <?php endif; ?>
        </div>
        <div class="row_dat">
          <div class="lbel">Email Address:</div>
          <div class="lbl_inpuCnt"><input type="text" name="email" id="email" /></div>
          <div class="clear"></div>
        </div>
        <div style="padding-left:120px;" class="row_dat">
          <a onclick="$('#forgotFrom').submit();" href="javascript:;" class="button mt10 clearfix">
            <span class="lft_area"></span>
            <span class="rpt_content">Send</span>
            <span class="rgt_area"></span>
          </a>
          <div class="clear"></div>
        </div>
      </div>
    </form>
  </div>
</div>

