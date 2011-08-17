
<?php if($rows):?>

  <?php foreach($rows as $row) :?>

    <div class="qaw-dlg-browse-row qaw-clearfix">
      
      <?php if(isset($row['cnt']) && $row['cnt'] > 0):?>
      
        <div class="qaw-dlg-expand-collapse">
          <a href="javascript:;" onclick="qaw_widget.get_sub_categories(this, <?php echo $row['id']?>)">
            [+] <?php echo $row[$item_field]?> (<?php echo $row['cnt']?>)
          </a>
        </div>
      
      <?php else:?>
        
        <div class="qaw-dlg-expand-collapse">
          <a href="javascript:;">
            <?php echo $row[$item_field]?>
          </a>
        </div>
        
      <?php endif;?>

      <?php $url = (array_key_exists('url', $row)) ? $row['url'] : $row['product_url']?>
      
      <div class="qaw-dlg-add-link"><a href="javascript:;" onclick="qaw_widget.add_link('<?php echo htmlentities($row[$item_field])?>', '<?php echo $url?>')">Add Link</a></div>
      
      <div class="qaw-clear"></div>
    </div>

  <?php endforeach;?>

<?php endif;?>