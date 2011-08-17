<table class="rpt_area" border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
      <th width="14">&nbsp;</th>
      <th>Product Id</th>
      <th>Product Title</th>
      <th>Product Description</th>
      <th>Get Code</th>
      <th>View Questions</th>
      <th>Add Question</th>
      <th>Action</th>
      <th>URL Stats</th>
    </tr>
    <?php if($products):?>

      <?php foreach($products as $product):?>

        <tr>
          <td>&nbsp;</td>
          <td><?php echo $product['qa_product_id']?></td>
          <td><?php echo $product['qa_product_title']?></td>
          <td><?php echo $product['qa_product_description']?></td>
          <td><a href="<?php echo base_url().'post/embedCode/'.$this->store_id.'/product/'.$product['id']?>">Get Code</a></td>
          <td><a href="<?php echo base_url().'moderate/index/'.$this->store_id.'/'.$product['id'].'/product'?>">View Questions</a></td>
          <td><a href="javascript:;" class="question-it" rel="<?php echo $product['id']?>/product">Add Question</a></td>
          <td><a href="javascript:;" class="edit-item" rel="<?php echo $product['id']?>/product">Edit</a></td>
          <td><a href="<?php echo base_url().'main/urlStats/'.$this->store_id.'/product/'.$product['id']?>">URL Stats</a></td>
        </tr>

      <?php endforeach;?>

    <?php else:?>

      <tr>
        <td colspan="9" style="text-align:center">No Records Found. <a href="<?php echo base_url()?>">Import Catalog.</a></td>
      </tr>

    <?php endif;?>
      
  </tbody>
</table>