
<script type="text/javascript">
  $(document).ready(function(e) {
    $("#productFrm").validate({
      messages:"This field is required",
      submitHandler: function(form) {
        success = true;
        if($('#threshold').val() != "") {
          var value = $('#threshold').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
          var intRegex = /^\d+$/;
          if(!intRegex.test(value) ) {
              $("#err_thr").text("Field must be numeric");
              $("#err_thr").show();
              success = false;
          
          }
         
        }
        if(success == true)
          form.submit();
        
       // do other stuff for a valid form
   	
   }
    });
    
    $(function(){$("a[title]").tooltip();});
    
  });
</script>


<div class="content_dashboard">
  
  <div class="heading_section  clearfix">
    <div class="head setting">Store Settings</div>
  </div>

  <form action="<?php echo (isset($store_info[0]) ? base_url() . 'post/createStore/update/' . $store_info[0]->qa_store_id : base_url() . 'post/createStore') ?>"  method="post" id="productFrm" class="form-cont">

    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">

        <div class="row_dat">
          <div id="message_store" class="message" style="display: none"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel">Store Name:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <input type="text" maxlength="15" class="account_med required" id="storeName" name="storeName" value="<?php echo (isset($store_info[0]->qa_store_name) ? $store_info[0]->qa_store_name : '') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="javascript:;" class="clickTip exampleTip" title="The Store Name will entered here."><img width="16" height="16" src="<?php echo base_url()?>images/frontend/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel">Who can vote me:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type="radio" name="vote" value="2"  <?php echo (isset($store_info[0]->qa_who_can_vote) && $store_info[0]->qa_who_can_vote == 2 ? 'checked = checked' : 'checked') ?>/>
              <span>Name</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="vote" value="4"  <?php echo (isset($store_info[0]->qa_who_can_vote) && $store_info[0]->qa_who_can_vote == 4 ? 'checked = checked' : '') ?>/>
              <span>Name and Email</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel">Who can comment me:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type="radio" name="comment" value="2"  <?php echo (isset($store_info[0]->qa_who_can_comment) && $store_info[0]->qa_who_can_comment == 2 ? 'checked' : 'checked') ?>/>
              <span>Name</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="comment" value="4"  <?php echo (isset($store_info[0]->qa_who_can_comment) && $store_info[0]->qa_who_can_comment == 4 ? 'checked' : '') ?>/>
              <span>Name and Email</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="condition_area"> </div>
        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Permissions:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type="radio" name="premission" value="view_Q&A_only"  <?php echo (isset($store_info[0]->qa_permission) && $store_info[0]->qa_permission == 'view_Q&A_only' ? 'checked' : 'checked') ?> />
              <span>View Q&A only</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="premission" value="post_only_questions"  <?php echo (isset($store_info[0]->qa_permission) && $store_info[0]->qa_permission == 'post_only_questions' ? 'checked' : '') ?>/>
              <span>Post only questions</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="premission" value="post_questions_answers"  <?php echo (isset($store_info[0]->qa_permission) && $store_info[0]->qa_permission == 'post_questions_answers' ? 'checked' : '') ?>/>
              <span>Post questions & answers</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Image upload option for:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type ="radio" name="image" value="1" <?php echo (isset($store_info[0]->image_option) && $store_info[0]->image_option == 1 ? 'checked' : 'checked' ) ?>/>
              <span>None</span>
            </label>
            <label class="clearfix radio">
              <input type ="radio" name="image" value="2" <?php echo (isset($store_info[0]->image_option) && $store_info[0]->image_option == 2 ? 'checked' : '' ) ?>/>
              <span>Questions</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="image" value="3" <?php echo (isset($store_info[0]->image_option) && $store_info[0]->image_option == 3 ? 'checked' : '' ) ?>/>
              <span>Answers</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="image" value="4" <?php echo (isset($store_info[0]->image_option) && $store_info[0]->image_option == 4 ? 'checked' : '' ) ?>/>
              <span>Both</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div class="lbel" style="width:190px">Moderation type:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type="radio" name="moderation" value="1" <?php echo (isset($store_info[0]->moderation_type) && $store_info[0]->moderation_type == 1 ? 'checked' : 'checked' ) ?>/>
              <span>None</span>
            </label>
            <label class="clearfix radio">
              <input type="radio" name="moderation" value="2" <?php echo (isset($store_info[0]->moderation_type) && $store_info[0]->moderation_type == 2 ? 'checked' : '' ) ?>/>
              <span>Automated</span>
            </label>
            <label class="clearfix radio">
              <input type="radio"  name="moderation" value="3" <?php echo (isset($store_info[0]->moderation_type) && $store_info[0]->moderation_type == 3 ? 'checked' : '' ) ?>/>
              <span>Staff</span>
            </label>
            <label class="clearfix radio">
              <input type="radio"  name="moderation" value="4" <?php echo (isset($store_info[0]->moderation_type) && $store_info[0]->moderation_type == 4 ? 'checked' : '' ) ?>/>
              <span>Store team</span>
            </label>
            <label class="clearfix radio">
              <input type="radio"  name="moderation" value="5" <?php echo (isset($store_info[0]->moderation_type) && $store_info[0]->moderation_type == 5 ? 'checked' : '' ) ?>/>
              <span>Staff & store team</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Vote Type:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type = "radio" name = "vote_type" value = "1" <?php echo (isset($store_info[0]->vote_type) && $store_info[0]->vote_type == 1 ? 'checked' : 'checked' ) ?>/>
              <span>None</span>
            </label>
            <label class="clearfix radio">
              <input type = "radio" name = "vote_type" value = "2" <?php echo (isset($store_info[0]->vote_type) && $store_info[0]->vote_type == 2 ? 'checked' : '' ) ?> />
              <span>Positive</span>
            </label>
            <label class="clearfix radio">
              <input type = "radio" name = "vote_type" value = "3" <?php echo (isset($store_info[0]->vote_type) && $store_info[0]->vote_type == 3 ? 'checked' : '' ) ?>/>
              <span>Positive & Negative</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Save provided image while uploading a product:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type = "radio" name = "product_image" value = "1" <?php echo (isset($store_info[0]->save_images_locally) && $store_info[0]->save_images_locally == 1 ? 'checked' : 'checked' ) ?>/>
              <span>Yes</span>
            </label>
            <label class="clearfix radio">
              <input type = "radio" name = "product_image" value = "2" <?php echo (isset($store_info[0]->save_images_locally) && $store_info[0]->save_images_locally == 2 ? 'checked' : '' ) ?> />
              <span>No</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Video option:</div>
          <div class="lbl_inpuCnt">
            <label class="clearfix radio">
              <input type ="radio" name = "video_option" value = "1" <?php echo (isset($store_info[0]->video_option) && $store_info[0]->video_option == 1 ? 'checked' : 'checked' ) ?>/>
              <span>No</span>
            </label>
            <label class="clearfix radio">
              <input type ="radio" name = "video_option" value = "2" <?php echo (isset($store_info[0]->video_option) && $store_info[0]->video_option == 2 ? 'checked' : '' ) ?> />
              <span>Yes</span>
            </label>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">Cart type:</div>
          <div class="lbl_inpuCnt">
            <?php echo select_tag('cart_type', $this->config->item('cart_types'), (isset($store_info[0]->cart_type) ? $store_info[0]->cart_type : ''))?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">FTP file name:</div>
          <div class="lbl_inpuCnt">
            <input type="text" id="ftp_file" name="ftp_file_name" value="<?php echo (isset($store_info[0]->ftp_file_name) ? $store_info[0]->ftp_file_name : '') ?>"/>
          </div>
          <div class="clear"></div>
        </div>
        <div style="width:190px" class="lbel wd165">Contributor Threshold</div>
        <div class="lbl_inpuCnt" style="width: 400px;">
          <input type="text" maxlength="3" id="threshold" name="threshold" value="<?php echo (isset($store_info[0]->qa_threshold) ? $store_info[0]->qa_threshold : '') ?>"/>&nbsp;<span id="err_thr" class="error" style="display: none"></span>
          </div>
          <div class="clear"></div>        
        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">&nbsp;</div>
          <input type="submit" class=" btn_save" tabindex="8" value="" name="edit_seller_info">
          <div class="clear"></div>
        </div>
      </div>
    </div>

  </form>

</div>