
<div id="sub_cat_<?php echo $category_id?>">
  
  <?php if($rows):?>

    <?php foreach($rows as $row) :?>

      <div class="row_white_lnk clearfix">

          <div class="expand_collase sub">
            <a href="javascript:;">
              <?php echo $row['qa_category_name']?>
            </a>
          </div>

        <div class="ad_lnk">
          <a onclick="add_link(this, <?php echo $row['id']?>, '<?php echo htmlentities($row['qa_category_name'])?>', '<?php echo $row['url']?>')" href="javascript:;">Add Link</a>
        </div>
      </div>

    <?php endforeach;?>

  <?php endif;?>
  
</div>