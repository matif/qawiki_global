

<?php foreach($products as $product) :?>

  <span class="qawiki-product">
    <span class="qawiki-product-title qawiki-ptitle-wide"><?php echo $product['qa_product_title']?></span>

    <?php if(!in_array($product['id'], $already_linked)): ?>

      <span class="qawiki-add-product" rel="<?php echo $product['id']?>"><a href="javascript:;">Add Product</a></span>

    <?php else:?>

      <span class="qawiki-product-added"><a href="javascript:;">Added</a></span>

    <?php endif;?>

    <span class="qawiki-product-desc"><?php echo $product['qa_product_description']?></span>
  </span>

<?php endforeach;?>