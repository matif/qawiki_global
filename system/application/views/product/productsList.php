
<div class="map-products">  
  <?php foreach($products as $product) :?>

    <div>
      <p>
        <strong><?php echo $product['qa_product_title']?></strong>
        <a href="javascript:;" rel="<?php echo $product['id']?>" class="map-link">Map</a>        
      </p>
      <p><?php echo $product['qa_product_id']?></p>
      <p><?php echo $product['qa_product_description']?></p>
    </div>

  <?php endforeach; ?>

</div>