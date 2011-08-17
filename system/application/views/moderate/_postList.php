
<?php if($post):?>

  <?php foreach($post as $key => $value) :?>

    <?php $post_status = post_status($value);?>

    <li>
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr>

          <td width="45" valign="middle"><span class="custom-chk"><span rel="<?php echo $value['qa_post_id']?>"></span></span></td>

          <td>
            <div id="question_<?php echo $value['qa_post_id']?>" class="<?php echo ($post_status == 'Approved') ? 'green' : ($post_status == 'Rejected' ? 'red' : 'orange')?>">
              <div class="yellow clearfix">
                <ul class="lft_lnks">
                  <li class="mod-status-app <?php echo ($post_status == 'Approved') ? 'selected' : 'change-mod-status'?>" rel="<?php echo $value['qa_post_id']?>|valid"><a href="javascript:;"><?php echo ($post_status == 'Approved') ? 'Approved' : 'Approve'?></a></li>
                  <li class="mod-status-rej <?php echo ($post_status == 'Rejected') ? 'selected-red' : 'change-mod-status'?>" rel="<?php echo $value['qa_post_id']?>|invalid"><a href="javascript:;"><?php echo ($post_status == 'Rejected') ? 'Rejected' : 'Reject'?></a></li>
                  <li><a href="javascript:;" class="export" rel="<?php echo $value['qa_post_id']?>">Export</a></li>
                  <li><a href="javascript:;" class="answer-it" rel="<?php echo $value['qa_post_id']?>">Answer</a></li>
                  <li><a href="javascript:;" class="email-it" rel="<?php echo $value['qa_post_id']?>">Email</a></li>
                  <li class="tooltip">Moderator Level: Expert ▼
                    <span class="dp">
                    <?php if(is_array($designations)):?>
                      <?php foreach($designations as $des):?>
                        <span class="level"><a href="javascript:;" <?php echo ($value['can_moderate'] == $des["designation_name"]) ? 'class="sel"' : ''?> rel="<?php echo $value['qa_post_id']?>" ><?php echo $des["designation_name"]?></a></span>
                      <?php endforeach;?>
                    <?php endif;?>
                    
<!--                      Standard
                      <span class="level"><a href="javascript:;" <?php echo ($value['can_moderate'] == 'manager') ? 'class="sel"' : ''?> rel="<?php echo $value['qa_post_id']?>" >Manager</a></span>
                      <span class="level"><a href="javascript:;" <?php echo ($value['can_moderate'] == 'expert') ? 'class="sel"' : ''?> rel="<?php echo $value['qa_post_id']?>" >Expert</a></span>-->
                    </span>
                  </li>
                </ul>
                <ul class="rgt_link">
                  <li><a href="javascript:;">Helpful?</a></li>
                  <li><a href="javascript:;" class="green_lnk">Yes [<?php echo ($value['pos_vote']) ? $value['pos_vote'] : 0?>]</a></li>
                  <li><a href="javascript:;" class="yellow_lnk">No[<?php echo ($value['neg_vote']) ? $value['neg_vote'] : 0?>]</a></li>
                  <li><a href="javascript:;" class="red_lnk">Flag[<?php echo $value['spam_count']?>]</a></li>
                  <li><a href="javascript:;">Answers[<?php echo $value['answers_count']?>]</a></li>																																																				
                </ul>
              </div>
              <h3><?php echo $value['qa_title']?></h3>
              <p><?php echo $value['qa_description']?></p>
              <div class="asked">Asked by <a href="javascript:;"><?php echo $value['user_name']?></a> @ <?php echo format_time($value['qa_created_at']);?> <a href="javascript:;" class="view-history" rel="<?php echo $value['qa_post_id']?>">View History</a> &nbsp;  <a href="javascript:;" class="view-answers" rel="<?php echo $value['qa_post_id']?>">View Answers</a></div>
              <div class="rpt_ar_cntr" id="answers_<?php echo $value['qa_post_id']?>" style="display:none"></div>

              <div class="history_panel" id="history_<?php echo $value['qa_post_id']?>" style="display:none"></div>
            </div>
          </td>

        </tr>
      </table>
    </li>

  <?php endforeach;?>

<?php else:?>

    <li style="text-align: center">No Records Found.</li>

  <?php endif;?>