
<table class="rpt_area" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <th width="14">&nbsp;</th>
      <th>Brand Id</th>
      <th>Brand Title</th>
      <th>View Products</th>
      <th>Get Code</th>
      <th>View Questions</th>
      <th>Add Question</th>
      <th>Action</th>
      <th>URL Stats</th>
    </tr>

    <?php if($brands):?>

      <?php foreach($brands as $brand):?>

        <tr>
          <td>&nbsp;</td>
          <td><?php echo $brand['qa_brand_id']?></td>
          <td><?php echo $brand['qa_brand_name']?></td>
          <td><a href="javascript:;" onclick="get_products(<?php echo $brand['id']?>, 'brand')">View Products</a></td>
          <td><a href="<?php echo base_url().'post/embedCode/'.$this->store_id.'/brand/'.$brand['id']?>">Get Code</td>
          <td><a href="<?php echo base_url().'moderate/index/'.$this->store_id.'/'.$brand['id'].'/brand'?>">View Questions</a></td>
          <td><a href="javascript:;" class="question-it" rel="<?php echo $brand['id']?>/brand">Add Question</a></td>
          <td><a href="javascript:;" class="edit-item" rel="<?php echo $brand['id']?>/brand">Edit</a></td>
          <td><a href="<?php echo base_url().'main/urlStats/'.$this->store_id.'/brand/'.$brand['id']?>">URL Stats</a></td>
        </tr>

      <?php endforeach;?>

    <?php else:?>

      <tr>
        <td colspan="9" style="text-align:center">No Records Found. <a href="<?php echo base_url()?>">Import Catalog.</a></td>
      </tr>

    <?php endif;?>
      
  </tbody>
</table>