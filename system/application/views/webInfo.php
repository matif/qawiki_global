
<script type="text/javascript">
  $(document).ready(function(e) {
    $("#productFrm").validate({
      
      messages:"This field is required"
    });
     $(function(){$("a[title]").tooltip();});    
  });
</script>


<div class="content_dashboard">

  <div class="heading_section  clearfix">
    <div class="head setting">Web Site Settings</div>
  </div>

  <form action="<?php echo (isset($web_info[0]))?base_url().'post/webInfo/'.$this->store_id."/edit" :base_url().'post/webInfo/'.$this->store_id ?>"  method="post" id="productFrm" class="constrain">

    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">        

        <div class="row_dat">
          <div style="width:190px" class="lbel">Site Domain:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <input type="text" class="account_med required" id="domainName" name="domainName" value="<?php echo (isset($web_info[0]->qa_site_name) ? $web_info[0]->qa_site_name : '') ?>" style="margin-right: 10px"/>            
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="Your Domain name that you want to integrate."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div style="width:190px" class="lbel">Login URL:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <input type="text" class="account_med required url" id="loginUrl" name="loginUrl" value="<?php echo (isset($web_info[0]->qa_login_url) && trim($web_info[0]->qa_login_url) ? $web_info[0]->qa_login_url : 'http://') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="Login Url Of the domain."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>        
        <div class="row_dat">
          <div style="width:190px" class="lbel">Thanks URL:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <input type="text" class="account_med required url" id="redirectParam" name="redirectParam" value="<?php echo (isset($web_info[0]->qa_thanks_url) && trim($web_info[0]->qa_thanks_url) ? $web_info[0]->qa_thanks_url : 'http://') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="Thanks URL of the domain."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>                     
        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">&nbsp;</div>
          <input type="submit" class=" btn_save" tabindex="8" value="" name="edit_seller_info">
          <div class="clear"></div>
        </div>
      </div>
    </div>

  </form>

</div>