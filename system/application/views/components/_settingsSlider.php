
<?php use_javascript('jcarousellite.min.js'); ?>
<script type="text/javascript">
//  $(document).ready(function(){  
//    $(".tray_inner").jCarouselLite({          
//      scroll: 1,
//      btnNext: ".defualt .move_next",
//      btnPrev: ".defualt .move_pre",
//      start:   0
//    });     
//  });  

</script>
<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Appearance and Function</div>
  </div>
  <div class="content_accordian">
    <div class="sticky_slider">
      <div class="sticky_cnt_area clearfix">
        <div class="move_pre disabled"><a href="javascript:;"></a></div>

        <div class="tray_inner">

          <ul>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="qa <?php echo ($tray_selected_thumb == 'appearance') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/appearance/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/appearance/' . $this->store_id ?>">Q &amp; A</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="q <?php echo ($tray_selected_thumb == 'question') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/question/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/question/' . $this->store_id ?>">Question</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="ans <?php echo ($tray_selected_thumb == 'answer') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/answer/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/answer/' . $this->store_id ?>">Answer</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="thank_you <?php echo ($tray_selected_thumb == 'thank_you') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/thank_you/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/thank_you/' . $this->store_id ?>">Thank You</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="contributor <?php echo ($tray_selected_thumb == 'contributor') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/contributor/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/contributor/' . $this->store_id ?>">Contributor</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="email_activity <?php echo ($tray_selected_thumb == 'activity_email') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/activity_email/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/activity_email/' . $this->store_id ?>">Email Activity</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="email_badges <?php echo ($tray_selected_thumb == 'badge_email') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/badge_email/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/badge_email/' . $this->store_id ?>">Email Badges</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="email_answer <?php echo ($tray_selected_thumb == 'answer_email') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/answer_email/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/answer_email/' . $this->store_id ?>">Email Answer</a></div>
              </div>
            </li>

            <li>
              <div class="thumb_Tray">
                <div class="thumbarea"><a class="email_question <?php echo ($tray_selected_thumb == 'question_email') ? 'tray-thumb-sel' : '' ?>" href="<?php echo base_url() . 'settings/question_email/' . $this->store_id ?>"></a></div>
                <div class="thumb_title"><a href="<?php echo base_url() . 'settings/question_email/' . $this->store_id ?>">Email Questions</a></div>
              </div>
            </li>

          </ul>

        </div>

        <div class="move_next"><a href="javascript:;"></a></div>							
      </div>
    </div>
  </div>
</div>

<div class="clear"></div>