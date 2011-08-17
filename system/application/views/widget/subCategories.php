
<div id="qaw-sub-cat-<?php echo $category_id?>">
  
  <?php if($rows):?>

    <?php foreach($rows as $row) :?>

      <div class="qaw-dlg-browse-row qaw-clearfix">

          <div class="qaw-dlg-expand-collapse qaw-dlg-sub-cat">
            <a href="javascript:;">
              <?php echo $row['qa_category_name']?>
            </a>
          </div>

        <div class="qaw-dlg-add-link"><a href="javascript:;"  onclick="qaw_widget.add_link('<?php echo htmlentities($row['qa_category_name'])?>', '<?php echo $row['url']?>')">Add Link</a></div>
        
        <div class="qaw-clear"></div>
      </div>

    <?php endforeach;?>

  <?php endif;?>
  
</div>