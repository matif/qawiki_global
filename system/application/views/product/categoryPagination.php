<form method="post" action="" id="productsFrm">
  <?php
  echo list_records_table($category, array(array(
      'text'     => 'qa_category_id',
      'heading'  => 'Category Id'
    ),array(
      'text'     => 'qa_category_name',
      'heading'  => 'Category Title'
    ),array(
      'link'     => 'javascript:;',
      'rel'      => '{qa_store_id}:|:{id}',
      'class'    => 'sub_categeries',
      'text'     => 'Sub Categeries',
      'heading'  => 'View Sub-Categories',
      'skip'     => (isset($sub_link))
    ),array(
      'callback' => "get_product_by_catagory('".base_url()."/post/getPostByCategory/{qa_store_id}', {id}, this, 'category')",
      'class'    => 'cat_product',
      'text'     => 'Products',
      'heading'  => 'View Products'
    ),array(
      'link'     => base_url().'post/embedCode/{qa_store_id}/category/{id}',
      'text'     => 'Code',
      'heading'  => 'Get Code'
    ),array(
      'rel'      => 'category:|:{id}',
      'class'    => 'question',
      'text'     => 'Add question',
      'heading'  => 'Add question',
      'skip'     => ($user_role == 'view' || $permission == 'view' ? true : false)
    ),array(
      'callback' => "viewQuestion({id}, 'category', this)",
      'class'    => 'view',
      'text'     => 'View Question',
      'heading'  => 'View Question'
    ),array(array(
      'link'     => 'javascript:;',
      'text'     => 'Edit',
      'heading'  => 'Action',
      'callback' => "edit_category({id},'category', this)",
      'skip'     => ($user_role == 'view' ? true : false)
      ),array(
        'text'     => ' / '
      )
      ,array(
        'text'         => 'Delete',
        'callback'     => "confirmation({id},'category')",
        'skip'     => ($user_role == 'view'  ? true : false)
      )
    ),array(
      'link'     => base_url().'main/urlStats/category/{id}',
      'target'   => '_blank',
      'text'     => 'URL Stats',
      'heading'  => 'URL Stats'
    )
  ));
  ?>
</form>
<div class="paging_gray">
  <?php echo $category_pager; ?>
</div>
