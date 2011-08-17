<?php

/* 

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

?>

<script type="text/javascript">
  var count = <?php echo count($views);?>;  
  function mapProduct(element, sid , des_id)
  {
    data = {
      's_id':sid,
      'des_id':des_id
    }
    
    $.post(base_url+'post/mapProducts/<?php echo $this->store_id?>', data, function(){            
      $(element).parent().parent().remove();      
      count --;
      if(count == 0)
      {
        window.location.href =base_url +'post/showProduct/<?php echo $this->store_id?>'
      }
    });
  }

</script>

<div class="header_rgt">  
  <label class="message error" style="display: none" id="error-map">These products are already mapped</label>
    <?php if($user_role == 'creator'):?>
    <h1>Link Products</h1>
    <?php else:?>
    <h1>Map Products</h1>
    <?php endif;?>
    <?php if(isset($views) && is_array($views) && count($views) > 0):?>
      <a style="float: right" href="<?php echo base_url()?>post/linkProducts/<?php echo $this->store_id; ?>">See Product and Add question>></a>
   <?php else:?>
      <a style="float: right" href="<?php echo base_url()?>post/showProduct/<?php echo $this->store_id; ?>">See Product and Add question>></a>
    <?php endif;?>

    <div class="clear"></div>

  </div>
  <p>These products exists in this store with different UPC code. You can replace the existing product data with newly added product</p>

<div id="mappedProducts"></div>

<form method="post" action="" id="productsFrm">
  <?php  
  echo list_records_table($views, array(array(
      'heading'  => 'Title',
      'text'     => 'qa_map_id'
    ),array(
      'text'     => 'qa_product_id',
      'heading'  => 'Destination Product'
    ),array(
      'id'       => "map",
      'text'     => 'Link Products',
      'heading'  => ($user_role == 'creator')?'Link Products':"Map Products",
      'callback' => 'mapProduct(this,{map_id}, {product_id})'
      
    )
  ));
?>
  
</form>