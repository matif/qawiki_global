
<div class="note_area">
  
  <h2>These similar questions need answers too! Do you know the answer?</h2>
  
  <?php foreach($similarPost as $key => $post): ?>
  
    <p><a href="javascript:;" onclick="load_answer_dialog_for_similar(<?php echo $post['qa_post_id']?>);"><?php echo $post['qa_title']?></a></p>
  
  <?php endforeach;?>
  
  <a class="button clearfix btn_clse" href="javascript:;" onclick="hideJModalDialog('answerDlg')">
    <span class="lft_area"></span>
    <span class="rpt_content">Close</span>
    <span class="rgt_area"></span>														
  </a>
  
</div>