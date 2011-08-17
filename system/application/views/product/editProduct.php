<?php echo link_js('jquery.validate.min') ?>
<script type="text/javascript">
  $(document).ready(function(e) {
    $("#editProduct").validate({
      messages:"This field is required"
    });
  });
</script>

<form class="constrain" action="<?php echo base_url()?>post/editProduct/<?php echo $this->store_id?>/<?php echo $this->product_id?>/submit" method="post" id="editProduct" enctype="multipart/form-data">
  <div> <label>Product Name</label><input type="text" class="required" name="name" value="<?php echo isset($product['qa_product_title'])?$product['qa_product_title']:"" ?>"></div>
  <div> <label>Product Description</label><input type="text" class="required" name="description" value="<?php echo isset($product['qa_product_description'])?$product['qa_product_description']:"" ?>"></div>
  <div><label>Product Id</label> <input type="text" class="required" name="product_id" value="<?php echo isset($product['qa_product_id'])?$product['qa_product_id']:"" ?>"/></div>
  
  <div><label>Add new link of Image</label> <input type="text" name="product_pic" value="<?php echo isset($product['product_image'])?$product['product_image']: "" ?>"/></div>  
  <div><input type="submit" value="Update"/></div>
</form>