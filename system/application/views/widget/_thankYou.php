

<?php echo widget_popup_banner($appearance, 'qaw-thank-you-header'); ?>

<div class="qaw-heading"><?php echo isset($row["title"]) ? $row["title"] : "Dear #UserName" ?></div>
<div class="qaw-sub-heading"><?php echo isset($row["sub_title"]) ? $row["sub_title"] : "Thank you for answering and growing community." ?></div>

<div class="qaw-cont-question-panel">
    
    <?php if(isset($row["email_footer"]) && trim($row["email_footer"])) :?>
    
      <?php echo $row["email_footer"]?>
    
    <?php else:?>
    
      <h3>Thank You for answer and growing community!!</h3>
      <ul>
        <li>Your answer will be post after review</li>
      </ul>
      
    <?php endif;?>
    
</div>

<div class="qaw-sub-heading">From,<br /><?php echo isset($row["from_email"]) ? $row["from_email"] : "Q&amp;A Support Team" ?></div>

<!--div class="email_footer">
  <a href="javascript:;" class="qaw-buton-gray qaw-button" id="qaw-thank-you-btn"><span>No Thanks Take back to the product page</span></a>
</div-->
<div class="clear"></div>

<?php echo $this->load->view('widget/_similarPost', array('similarPost' => $similarPost), true); ?>

<div class="qaw-clear"></div>