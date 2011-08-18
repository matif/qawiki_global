<script type="text/javascript">
  $("#recent_contents").change(function(){
    recent = $(this).val();    
    doAjax('post', base_url+'settings/saveAppearanceConfig/'+store_id, {value: recent, type:"sub_title"}, 'html', function(data){

    });    
  });

</script>
<?php if ($type == "recent"): ?>
<div style="padding: 10px 0">
    <input type = "text" id = "search" value="" class="qaw-search-text" /> 
    <?php echo widget_button('search_button', $default_button, 'Search', $this->custom_config['buttons']['search_button'], (isset($widget_configuration) ? $widget_configuration : null));?>
    <div class="clear"></div>
  </div>
  <div style="padding-bottom: 30px;">
    <div style="width:190px" class="lbel"><strong> Recent Tab Contents:</strong></div>
    <div class="lbl_inpuCnt" style="width:auto">
      
      <?php echo select_tag('recent_contents', $this->custom_config['recent_tag_contents'], (isset($sub_title) ? $sub_title : ''), array('class' => 'fr wid_160px'))?>
      
    </div>
  </div>
<?php else: ?>

  <div style="padding: 10px 0">
    <input type = "text" id = "search" value="" class="qaw-search-text" /> 
    <?php echo widget_button('search_button', $default_button, 'Search', $this->custom_config['buttons']['search_button'], (isset($widget_configuration) ? $widget_configuration : null));?>
    <div class="clear"></div>
  </div>

  <div class="row_tabs clearfix">
    <div class="use_img"><a href="javascript:;"><img src="<?php echo base_url() ?>images/frontend/user_img.gif" alt="User Name" title="User Name" width="71" height="60" /></a></div>
    <div class="user_detail">
      <h2><a href="javascript:;" class="qaw-link">Is this a good question?</a></h2>
      <p>
        Asked by <a href="javascript:;" class="qaw-link">USER123</a> Helpful?
        <?php if (trim($vote_negative_image)): ?>

          <span class="qaw-vote qaw-vote-up">0</span>
          <span class="qaw-vote qaw-vote-down">0</span>

        <?php else: ?>

          <a href="javascript:;" class="qaw-action"><strong>Yes(0)</strong></a> 
          <a href="javascript:;" class="qaw-action"><strong>No(0)</strong></a>

        <?php endif; ?>

        <a href="javascript:;" class="qaw-action"><strong>Flag</strong></a>
      </p>
      <div class="comment_are clearfix">
        <!--                        <div class="btn_answer fl"><a href="javascript:;"></a></div>-->
        <div>
          <?php echo widget_button('answer_it', $default_button, 'Answer', $this->custom_config['buttons']['answer_it'], (isset($widget_configuration) ? $widget_configuration : null));?>
        </div>  
        <div class="share_panel">
          <a href="javascript:;" class="qaw-link">3 Answers </a><a href="javascript:;" style="background:none" class="qaw-action">Share  ▼ </a>
        </div>
      </div>
    </div>
  </div>

<?php endif; ?>