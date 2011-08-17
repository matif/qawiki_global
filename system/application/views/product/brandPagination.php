<form method="post" action="" id="productsFrm">
<?php  
  echo list_records_table($brands, array(array(
      'text'     => 'qa_brand_id',
      'heading'  => 'Brand Id'
    ),array(
      'text'     => 'qa_brand_name',
      'heading'  => 'Brand Title'
    ),array(
      'callback' => "get_product_by_catagory('".base_url()."/post/getPostByCategory/{qa_store_id}', {id}, this, 'brand')",
      'class'    => 'cat_product',
      'text'     => 'Products',
      'heading'  => 'View Products'
    ),array(
      'link'     => base_url().'post/embedCode/{qa_store_id}/brand/{id}',
      'text'     => 'Code',
      'heading'  => 'Get Code'
    ),array(
      'rel'      => 'Brand:|:{id}',
      'class'    => 'question',
      'text'     => 'Add question',
      'heading'  => 'Add question',
      'skip'     => ($user_role == 'view' || $permission == 'view' ? true : false)
    ),array(
      'callback' => "viewQuestion({id}, 'brand', this)",
      'class'    => 'view',
      'text'     => 'View Question',
      'heading'  => 'View Question'
    ),array(array(
      'link'     => 'javascript:;',
      'text'     => 'Edit',
      'heading'  => 'Action',
      'callback' => "edit_category({id},'brand',this)",
      'skip'     => ($user_role == 'view' ? true : false)
    ),array(
        'text'     => ' / '
    ),array(
        'text'         => 'Delete',
        'callback'     => "confirmation({id},'brand')",
        'skip'     => ($user_role == 'view' ? true : false)
      )
    ),array(
      'link'     => base_url().'main/urlStats/brand/{id}',
      'target'   => '_blank',
      'text'     => 'URL Stats',
      'heading'  => 'URL Stats'
    )
  ));
?>
</form>
<div class="paging_gray">
  <?php echo $brand_pager; ?>
</div>
