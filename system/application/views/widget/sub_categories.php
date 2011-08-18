
<?php if(count($sub_categories) > 0):?>

  <?php foreach($sub_categories as $category) :?>

    <span class="qawiki-cat qawiki-sub-cat" rel="category/<?php echo $category['id']?>"><a href="javascript:;"><?php echo trim_text($category['qa_category_name'])?></a> (<?php echo $category['products_count']?>)</span>

  <?php endforeach;?>

<?php endif;?>