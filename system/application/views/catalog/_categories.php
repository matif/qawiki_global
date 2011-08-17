
<table class="rpt_area" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <th width="14">&nbsp;</th>
      <th>Category Id</th>
      <th>Category Title</th>
      <th>View Products</th>
      <th>Get Code</th>
      <th>View Questions</th>
      <th>Add Question</th>
      <th>Action</th>
      <th>URL Stats</th>
    </tr>
    
    <?php if($categories):?>

      <?php foreach($categories as $category):?>

        <tr>
          <td>&nbsp;</td>
          <td><?php echo $category['qa_category_id']?></td>
          <td><?php echo $category['qa_category_name']?></td>
          <td><a href="javascript:;" onclick="get_products(<?php echo $category['id']?>, 'category')">View Products</a></td>
          <td><a href="<?php echo base_url().'post/embedCode/'.$this->store_id.'/category/'.$category['id']?>">Get Code</td>
          <td><a href="<?php echo base_url().'moderate/index/'.$this->store_id.'/'.$category['id'].'/category'?>">View Questions</a></td>
          <td><a href="javascript:;" class="question-it" rel="<?php echo $category['id']?>/category">Add Question</a></td>
          <td><a href="javascript:;" class="edit-item" rel="<?php echo $category['id']?>/category">Edit</a></td>
          <td><a href="<?php echo base_url().'main/urlStats/'.$this->store_id.'/category/'.$category['id']?>">URL Stats</a></td>
        </tr>

      <?php endforeach;?>

    <?php else:?>

      <tr>
        <td colspan="9" style="text-align:center">No Records Found. <a href="<?php echo base_url()?>">Import Catalog.</a></td>
      </tr>

    <?php endif;?>
      
  </tbody>
</table>