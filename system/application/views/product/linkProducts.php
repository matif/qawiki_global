

<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>css/jquery/jquery-ui.custom.css" />

<script type="text/javascript">
  var product_id = null;
  var map_id = null;
  var $parents = null;
  
  $(document).ready(function(){
    $('.map-product').bind('click', function(){
      product_id = $(this).attr('rel');
      $parents = $(this).parent().parent();
      product_info = $parents.find('form', 0).serialize();
      $("#product_info").val(product_info);
      show_products_dialog();
    });
    
    $('.map-link').live('click', function(){
      map_id = $(this).attr('rel');
      $("#map_id").val(map_id);
      $(this).parent().append('<img src="<?php echo base_url()?>images/frontend/ajax-progress.gif" align="right" />');
      $('.map-link').remove();
      save_product_mapping(this);
    });
  });
  
  function show_products_dialog()
  {
    loadJModalDialog('<?php echo base_url().'post/productsList/'.$store_id?>', {title: 'Map Product', width: 450, height: 250});
  }
  
  function save_product_mapping(element)
  {
    var col = new Array();    
    
    for(var i = 0; i < $("#count").val();i++)
    {
        col[i] = $("#col_"+i).val();
    }
    
    form1 = $("#mainForm").serialize();

    data =form1+"&"+product_info;
    
    $.post('<?php echo base_url()?>post/saveProductMapping/', data, function(){
      $('a[rel='+product_id+']').parent().html('Mapped!');
      hideJModalDialog();
    });
  }
</script>

<!--<a href="<?php echo base_url().'post/'.($this->session->userdata('products') ? 'mapProducts' : 'showProduct').'/'.$store_id?>">Skip this step</a>-->

<table cellpadding="0" cellspacing="0" class="stores-list" border="1">

  <thead>
    <tr>
      <th>Product Code</th>
      <th>Product Title</th>
      <th>Product Description</th>
      <th width="100" align="center">Map Product</th>
    </tr>
  </thead>
  
  <tbody>    
    <?php if(isset($products) && count($products) > 0):?>

      <?php foreach($products as $product) :?>

        <tr>
          <td>
            <?php echo $product[0]['product id']?>
            <form action="" method="" >
              <input type="hidden" name="p_id" value="<?php echo isset($product[0]['product id'])?$product[0]['product id']: ""?>"/>
              <input type="hidden" name="product_title" value="<?php echo isset($product[0]['title'])?$product[0]['title']:""?>" />
              <input type="hidden" name="product_description" value="<?php echo isset($product[0]['description'])?$product[0]['description']:""?>" />
              <input type="hidden" name="category_name" value="<?php echo isset($product[0]['category name'])? $product[0]['category name']:""?>" />
              <input type="hidden" name="category_id" value="<?php echo isset($product[0]['category id']) ?$product[0]['category id']:""?>" />
              <input type="hidden" name="brand_name" value="<?php echo isset($product[0]['brand name'])? $product[0]['brand name']:""?>" />
              <input type="hidden" name="brand_id" value="<?php echo isset($product[0]['brand id']) ?$product[0]['brand id']:""?>" />
              <input type="hidden" name="product_url" value="<?php echo isset($product[0]['product url']) ?$product[0]['product url']:""?>" />
              <input type="hidden" name="image_url" value="<?php echo isset($product[0]['image url']) ?$product[0]['image url']:""?>" />
              <input type="hidden" name="parent_id" value="<?php echo isset($product[0]['parent id']) ?$product[0]['parent id']:""?>" />
            </form>
          </td>

          <td><?php echo isset($product[0]['title'])?$product[0]['title']:""?></td>

          <td><?php echo isset($product[0]['description'])?$product[0]['description']:""?></td>
          <td align="center"><a href="javascript:;" rel="<?php echo isset($product[0]['product id'])?$product[0]['product id']: ""?>" class="map-product">Map Product</a></td>
        </tr>

      <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">No record Found</td></tr>
    <?php endif; ?>
    
  </tbody>

</table>

<form action="" method="" id="mainForm">
  <?php $i = 0;?>
  <?php if(isset($columns)):?>
    <?php foreach($columns as $col):?>
  <input type="hidden" name="hid_<?php echo $i;?>" value="<?php echo $col?>"/>
    <?php $i++;?>
    <?php endforeach;?>
  <?php endif;?>
  <input type="hidden" name="associate"  value="<?php echo isset($association)?$association:""?>"/>
  <input type="hidden" id="store_id" value="<?php echo $store_id?>" name="store_id"/>
  <input type="hidden"  value="<?php echo isset($count)?$count:""?>" name="count"/>
   <input type="hidden" id="map_id" value="0" name="map_id"/>
</form>
