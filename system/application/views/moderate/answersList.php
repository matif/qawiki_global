
<?php if($post):?>

  <?php foreach($post as $key => $value) :?>

    <div class="row_rpt">
      <h3><?php echo $value['qa_title']?></h3>
      <p><?php echo $value['qa_description']?></p>
      <div class="asked">Asked by <a href="javascript:;"><?php echo $value['user_name']?></a> @ <?php echo format_time($value['qa_created_at']);?></div>													
    </div>

  <?php endforeach;?>

<?php else:?>

  <p>No answer given yet!</p>

<?php endif;?>