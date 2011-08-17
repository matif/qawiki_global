

<div class="qawiki-categories">

  <?php if($autonomous_count > 0):?>

    <span class="qawiki-cat" rel="product/-1"><a href="javascript:;">Autonomous</a> (<?php echo $autonomous_count?>)</span>

  <?php endif;?>

  <?php if(count($categories) > 0):?>

    <h3>Categories</h3>

    <?php foreach($categories as $category) :?>

      <span class="qawiki-cat" rel="category/<?php echo $category['id']?>/parent"><a href="javascript:;"><?php echo trim_text($category['qa_category_name'])?></a> (<?php echo $category['products_count']?>)</span>

    <?php endforeach;?>

  <?php endif;?>

  <?php if(count($brands) > 0):?>

    <h3>brands</h3>

    <?php foreach($brands as $brand) :?>

    <span class="qawiki-cat" rel="brand/<?php echo $brand['id']?>"><a href="javascript:;"><?php echo trim_text($brand['qa_brand_name'])?></a> (<?php echo $brand['products_count']?>)</span>

    <?php endforeach;?>

  <?php endif;?>

</div>

<div id="qawiki-products">

</div>

<div class="qawiki-clear"></div>