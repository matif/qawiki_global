

<div class="content_pop">

  <?php echo widget_popup_banner($appearance, 'qaw-thank-you-header');?>

  <div class="heading_edit no_bdr"><?php echo isset($row["title"])?$row["title"]:"Dear #UserName"?>,</div>
  <div class="qtn_pan normal_text"><?php echo isset($row["sub_title"])?$row["sub_title"]:"Thank you for answering and growing community."?></div>
   <div class="question_panel">
   <span class="editable-text">
     
    <?php 
      echo isset($row["email_footer"]) ? 
        $row["email_footer"]
        : '<h3>Thank You for answer and growing community!!</h3>
        <ul>
          <li>Your answer will be post after review</li>              
        </ul>'
    ?>
    </span>
   <a href="javascript:;"><img rel ="email_footer" width="16" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a>
  </div>	
  <div class="qtn_pan normal_text">From, <br /><?php echo isset($row["from_email"])?$row["from_email"]:"Q&amp;A Support Team"?></span></div>
  <div class="email_footer">
    <?php echo widget_button('thank_you', $this->custom_config['default_button'], 'No Thanks Take back to the product page', $this->custom_config['buttons']['thank_you'], $appearance);?>
  </div>
  
  <div class="qaw-clear"></div>

</div>