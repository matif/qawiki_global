
<?php if($rows):?>

  <?php foreach($rows as $row) :?>

    <div class="row_white_lnk clearfix">
      
      <?php if(isset($row['cnt']) && $row['cnt'] > 0):?>
      
        <div class="expand_collase">
          <a href="javascript:;" onclick="get_sub_categories(this, <?php echo $row['id']?>)">
            [+] <?php echo $row[$item_field]?> (<?php echo $row['cnt']?>)
          </a>
        </div>
      
      <?php else:?>
        
        <div class="expand_collase">
          <a href="javascript:;">
            <?php echo $row[$item_field]?>
          </a>
        </div>
        
      <?php endif;?>

      <?php $url = (array_key_exists('url', $row)) ? $row['url'] : $row['product_url']?>
      
      <div class="ad_lnk"><a href="javascript:;" onclick="add_link(<?php echo $row['id']?>, '<?php echo htmlentities($row[$item_field])?>', '<?php echo $url?>')">Add Link</a></div>
    </div>

  <?php endforeach;?>

<?php endif;?>