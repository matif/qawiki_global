
<script type="text/javascript">
  $(document).ready(function(e) {
    $("#productFrm").validate({
      messages:"This field is required",
      submitHandler: function(form) {
        success = true;
        var question = $('#question').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        var answer   = $('#answer').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        var q_liked  = $('#question_liked').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        var a_liked  = $('#answer_liked').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        
        var intRegex = /^\d+$/;
        if($("#question").val() == "" && $("#question_liked").val() == "" && $("#answer").val() == "" && $("#answer_liked").val() == "")
        {
          $("#err_criteria").slideDown("fast");
          success = false;
        }        

        else if((question != "" &&!intRegex.test(question))|| (answer != "" && !intRegex.test(answer))||(q_liked  != "" && !intRegex.test(q_liked))||(a_liked  != "" &&!intRegex.test(a_liked))) {
             $("#err_criteria").text("Field must be numeric: Question, Answer, Question Liked, Answer Liked");
             $("#err_criteria").slideDown("fast");
             success = false;          
        }        
        if(success == true)
          form.submit();
        
        // do other stuff for a valid form
   	
      }
    });
    $("#milestone").keyup(function(){
      $.post("<?php echo base_url()?>settings/checkMilestoneDuplication/<?php echo $this->store_id?>/"+$("#milestone").val(),function(data){
        if(data == "error")
        {
          $("#duplicate").show();
          $("#save_btn").attr("disabled", "true");
        } else{
          $("#duplicate").hide();
          $("#save_btn").removeAttr("disabled");
        }
      });
    });
    
    $(function(){$("a[title]").tooltip();});
    
  });
</script>

<div class="<?php echo (isset($milestone_edit) && is_array($milestone_edit))?'':'content_dashboard'?>">
  <div class="<?php echo (isset($milestone_edit) && is_array($milestone_edit))?'':'heading_section  clearfix'?>">
    <?php if(isset($milestone_edit) && is_array($milestone_edit)):?>
<!--      <div class="head setting">Edit Milestones</div>-->
   <?php else:?>
      <div class="head setting">Add Milestone</div>      
    <?php endif;?>
  </div>  

  <form action="<?php echo (isset($milestone_edit[0]) ? base_url() . 'teams/saveMilestone/' . $this->store_id."/edit/".$milestone_edit[0]["id"] : base_url() . 'teams/saveMilestone/'.$this->store_id)?>"  method="post" id="productFrm" class="constrain">
    <div class="content_accordian">
      <div id="content_1" class="<?php echo (isset($milestone_edit) && is_array($milestone_edit))?'':'disp_content_white'?>">
        <div class="error row_dat" id="err_criteria" style="display: none;"> Define at least one of them: Question, Answer, Question Liked, Answer Liked</div>
        <div class="row_dat">
          <div id="message_store" class="message" style="display: none"></div>
        </div>
        <div class="row_dat">
          <div style="width:190px" class="lbel">Milestone:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" class="account_med required" id="milestone" name="milestone" value="<?php echo (isset($milestone_edit[0]["name"]) ? $milestone_edit[0]["name"] : '') ?>" style="margin-right: 10px"/>            
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="The Milestone name will entered here."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div id="duplicate" class ="error" style="display:none"> This milestone is already created</div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div style="width:190px" class="lbel"><strong> Criteria:</strong></div>          
          <div class="clear"></div>
        </div>        
        
        <div class="row_dat">
          <div style="width:190px" class="lbel">Number of Questions:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" class="account_med " id="question" name="question" maxlength="3" value="<?php echo (isset($milestone_edit[0]["question"]) ? $milestone_edit[0]["question"] : '') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="The Criteria for question will entered here."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel">Number of Answers:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" class="account_med " id="answer" maxlength="3" name="answer" value="<?php echo (isset($milestone_edit[0]["answer"]) ? $milestone_edit[0]["answer"] : '') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="The Criteria for answer will entered here."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div style="width:190px" class="lbel">Number of Questions Liked:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" maxlength="3" class="account_med " id="question_liked" name="question_liked" value="<?php echo (isset($milestone_edit[0]["question_liked"]) ? $milestone_edit[0]["question_liked"] : '') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="The Criteria for liked question will entered here."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div style="width:190px" class="lbel">Number of Answers Liked:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" maxlength="3" class="account_med" id="answer_liked" name="answer_liked" value="<?php echo (isset($milestone_edit[0]["answer_liked"]) ? $milestone_edit[0]["answer_liked"] : '') ?>" style="margin-right: 10px"/>
          </div>
          <div class="hlp_ares"><a href="#" class="clickTip exampleTip" title="The Criteria for liked answer will entered here."><img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a></div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">&nbsp;</div>
          <input type="submit" class=" btn_save" id="save_btn" tabindex="8" value="" name="edit_seller_info">
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </form>


