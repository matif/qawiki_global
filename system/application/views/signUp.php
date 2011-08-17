<script type="text/javascript">
  $(document).ready(function(e) {
    $("#signUp").validate({
      rules:
        {
        name:"required",
        email:{
          email:true,
          required:true
        },
        password:"required",
        confirmPassword:"required"
      },
      messages:"This field is required" ,
      submitHandler: function(form) {
        submitVal = 0;
        if($('#password').val() != $('#confirmPassword').val())
        {
          $('#passwor_error').show();
          submitVal =1;
        }

        data={
          "name":$('#name').val(),
          "email" : $('#email').val()
        }
        $.ajax({
          type: "POST",
          url: base_url+"index.php/register/checkDupication",
          data:data,
          dataType: "html",
          success: function(msg)
          {
            if (!$('#name').val().match("^[a-z0-9A-Z'.\s]{1,50}$")) 
            {
              $('#invalid_error').html("Only alpha-numeric values are allowed in the name field");
              $('#invalid_error').show();
              $('#email_error').hide();
              $('#name_error').hide();
            }else if(msg == -1 ) {
              $('#email_error').hide();
              $('#name_error').css("display","block")
              $('#invalid_error').hide();
            }else if(msg == -2)
            {
              $('#name_error').hide();
              $('#email_error').css("display","block");
              $('#invalid_error').hide();
            }else if(msg == -3){
              $('#name_error').css("display","block");
              
              $('#email_error').css("display","block");
              $('#invalid_error').hide();
            }else if(submitVal == 0){
              form.submit();
            }
          }
        });
      }
    });
    $('#name, #email, #password, #confirmPassword, #recaptcha_response_field')
    .listenForEnter()
    .bind('pressedEnter', function()
    {
      $("#signUp").submit();
    });    
  });
</script>


<div class="content_dashboard" style="width: 375px; margin: auto">
  <div class="heading_section clearfix">
    <div style="width:280px" class="head info">Sign UP</div>
    <div id="display_1" class="accordian_close"><a href="javascript:;"></a></div>
  </div>
  <div class="content_accordian">
    <form action="<?php echo base_url() ?>index.php/register/signup" method="post" id="signUp" class="form-cont">
      <div class="disp_content_white" id="content_1">
        <div class="row_dat">
          <div style="color:#F00" id="message_account" class="message">
          </div>
        </div>
        <div class="row_dat">
          <div class="lbel">User Name:</div>
          <div class="lbl_inpuCnt">
            <input type="text" name="name" id="name"/><span id="name_error" class="error" style="display: none;">This name is already registered</span><div id="invalid_error" class ="error"></div>
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel">Email:</div>
          <div class="lbl_inpuCnt">
            <input type="text" name="email" id="email"/><span id="email_error" class="error" style="display: none;">This email is already registered</span>
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel">Password:</div>
          <div class="lbl_inpuCnt">
            <input type="password" name="password" id="password"/>
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <div class="lbel">Confirm Password:</div>
          <div class="lbl_inpuCnt">
            <input type="password" name="confirmPassword" id="confirmPassword"/><label class="error" id="passwor_error" style="display: none">Password does not match</label>
          </div>
          <div class="clear"></div>
        </div>
        <div class="row_dat">
          <?php $publickey = $this->config->item('public_key'); ?>

          <?php echo recaptcha_get_html($publickey); ?><span id="captcha_image"></span>&nbsp;<span id="captcha_span"></span>
          <?php if (isset($this->data)): ?>
            <label class="error"><?php echo $this->data->html ?></label>
          <?php endif; ?>
        </div>
        <div style="padding-left:120px;" class="row_dat">
          <a onclick="$('#signUp').submit();" href="javascript:;" class="button mt10 clearfix">
            <span class="lft_area"></span>
            <span class="rpt_content">Sign Up</span>
            <span class="rgt_area"></span>
          </a>
          <div class="clear"></div>
        </div>
      </div>
    </form>
  </div>
</div>