
<div class="qaw-similar-post">
  
  <h2>These similar questions need answers too! Do you know the answer?</h2>
  
  <?php foreach($similarPost as $key => $post): ?>
  
    <p><a href="javascript:;" rel="<?php echo $post['qa_post_id']?>" id="qaw-answer-it"><span><?php echo $post['qa_title']?></span></a></p>
  
  <?php endforeach;?>
    
  <a href="javascript:;" class="qaw-flex-button qaw-clearfix qaw-dlg-btn-close" onclick="qaw_widget.qawiki_html.remove_dialog()">
    <span class="qaw-flex-button-left"></span>
    <span class="qaw-flex-button-mid">Close</span>
    <span class="qaw-flex-button-right"></span>
  </a>
  
</div>