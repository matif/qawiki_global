<form method="post" action="" id="productsFrm">

  <?php
    echo list_records_table($products, array(array(
        'value'    => '{id}',
        'heading'  => 'Select',
        'type'     => 'checkbox',
        'name'     => 'products[]',
        'skip'     => ($user_role == 'view' ? true : false)
      ),array(
        'text'     => 'qa_product_id',
        'heading'  => 'Product Code'
      ),array(
        'text'     => 'qa_product_title',
        'url'      => 'product_url',
        'heading'  => 'Product Title'
      ),array(
        'text'     => 'qa_product_description',
        'heading'  => 'Product Description'
      ),array(
        'link'     => base_url().'post/embedCode/{qa_store_id}/product/{id}',
        'text'     => 'Code',
        'heading'  => 'Get Code'
      ),array(
        'rel'      => 'product:|:{id}',
        'class'    => 'question',
        'text'     => 'Add Question',
        'heading'  => 'Add Question',
        'skip'     => ($user_role == 'view' || $permission == 'view' ? true : false)
      ),array(
        'callback' => "viewQuestion({id}, 'product', this)",
        'text'     => 'View Question',
        'heading'  => 'View Question'
      ),array(
        'src'      => 'product_image',
        'height'   => '100',
        'width'    => '100',
        'default'  => 'No Image Found',
        'heading'  => 'Product Image'
      ),array(array(
      'link'     => 'javascript:;',
      'text'     => 'Edit',
      'heading'  => 'Action',
      'callback' => "edit_product({id},this)",
      'skip'     => ($user_role == 'view'  ? true : false)
      ),
      array(
        'text'     => ' / '
      )
      ,array(
        'text'         => 'Delete',
        'callback'     => "confirmation({id},'product')",
        'skip'     => ($user_role == 'view'  ? true : false)
      )
      ),array(
        'link'     => base_url().'main/urlStats/product/{id}',
        'target'   => '_blank',
        'text'     => 'URL Stats',
        'heading'  => 'URL Stats'
      )
    ));
  ?>
</form>
<div class="paging_gray">
  <?php echo $products_pager; ?>
</div>