<?php echo use_javascript('moderate'); ?>

<?php echo grid_libraries() ?>

<script type="text/javascript">
var moderation_type = <?php echo $this->moderation_type ?>;
</script>

<div id="spams"><a href="<?php echo base_url() ?>moderator/spamPosts/<?php echo $this->store_id ?>">View Spam Post</a></div><br/>

<div>
<strong>Search Stores</strong>
<input type="text" id="searchbox" value="Type Your text Here" onclick="this.value=''" onblur="(this.value == ''? this.value ='Type Your text Here':'')"/> <span style="display: none" id="no_record">No Record Found</span>
<div class="clear"></div>
</div>
<br/>

<?php if (isset($this->store_info)) : ?>

<div class="content_dashboard">
<?php echo grid_title_html('Categories', 'close'); ?>
<div class="content_accordian">
<div class="disp_content_white noborder nopad">

<table id="categories_list"></table>
<div id="categories_pager"></div>
<?php
          echo render_grid('categories_list', base_url() . 'moderator/category_moderator/' . $this->store_id, array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#categories_pager',
            'sort_column' => $grid_column,
            'sort_order' => $grid_order,
            'head' => array('Id', 'Category Id', 'Category Name', 'View Answer', 'View Questions'),
            'columns' => array(array(
                'name' => 'Id',
                'index' => 'id',
                'width' => 310
              ), array(
                'name' => 'Category Id',
                'index' => 'qa_category_id',
                'width' => 310
              ), array(
                'name' => 'Category Name',
                'index' => 'qa_category_name',
                'width' => 310
              ), array(
                'name' => 'View Answers',
                'sortable' => false,
                'width' => 103
              ), array(
                'name' => 'View Questions',
                'sortable' => false,
                'width' => 103
              )
            ))
          );
        ?>

</div>
</div>
</div>
<div class="content_dashboard">
<?php echo grid_title_html('Brands', 'open'); ?>
<div class="content_accordian" style="display: none">
<div class="disp_content_white noborder nopad">

<table id="brands_list"></table>
<div id="brands_pager"></div>
<?php
          echo render_grid('brands_list', base_url() . 'moderator/brand_moderator/' . $this->store_id, array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#brands_pager',
            'sort_column' => $grid_column,
            'sort_order' => $grid_order,
            'head' => array('Id', 'Brand Id', 'Brand Name', 'View Answers', 'View Questions'),
            'columns' => array(array(
                'name' => 'Id',
                'index' => 'id',
                'width' => 310
              ), array(
                'name' => 'Brand Id',
                'index' => 'qa_brand_id',
                'width' => 310
              ), array(
                'name' => 'Brand Name',
                'index' => 'qa_brand_name',
                'width' => 310
              ), array(
                'name' => 'View Answers',
                'sortable' => false,
                'width' => 103
              ), array(
                'name' => 'View Questions',
                'sortable' => false,
                'width' => 103
              )
            ))
          );
        ?>
</div>
</div>
</div>

<div class="content_dashboard">
<?php echo grid_title_html('Products', 'open'); ?>
<div class="content_accordian" style="display: none">
<div class="disp_content_white noborder nopad">
<table id="products_list"></table>
<div id="products_pager"></div>

<?php
          echo render_grid('products_list', base_url() . 'moderator/product_moderator/' . $this->store_id, array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#products_pager',
            'sort_column' => $grid_column,
            'sort_order' => $grid_order,
            'select_row_callback' => 'select_product',
            'head' => array('Id', 'Product Code', 'Product Title', 'View Answers', 'View Questions'),
            'columns' => array(array(
                'name' => 'Id',
                'index' => 'id',
                'width' => 310
              ), array(
                'name' => 'Product Code',
                'index' => 'qa_product_id',
                'width' => 310
              ), array(
                'name' => 'Product Title',
                'index' => 'qa_product_title',
                'width' => 310
              ), array(
                'name' => 'View Answers',
                'sortable' => false,
                'width' => 103
              ), array(
                'name' => 'View Questions',
                'sortable' => false,
                'width' => 103
              )
            ))
          );
        ?>
</div>
</div>
</div>

<div class="content_dashboard" id="viewQuestion" style="display: none">
<?php echo grid_title_html('Questions', 'close'); ?>
<div class="content_accordian">
<div class="disp_content_white noborder nopad">

<table id="question_list"></table>
<div id="question_pager"></div>

<?php
          echo render_grid('question_list', '', array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#question_pager',
            'sort_column' => 'qa_post_id',
            'sort_order' => 'asc',
            'head' => array('Title', 'Description', 'Moderate'),
            'columns' => array(array(
                'name' => 'Title',
                'index' => 'qa_title',
                'width' => 400
              ),array(
                'name' => 'Description',
                'index' => 'qa_description',
                'width' => 550
              ),array(
                'name' => 'Moderate',
                'sortable' => false,
                'width' => 196
              )
            )
          ));
        ?>
</div>
</div>
</div>

<div class="content_dashboard" id="viewAnswer" style="display: none">
<?php echo grid_title_html('Answers', 'close'); ?>
<div class="content_accordian">
<div class="disp_content_white noborder nopad">

<table id="answer_list"></table>
<div id="answer_pager"></div>

<?php
          echo render_grid('answer_list', '', array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#answer_pager',
            'sort_column' => 'qa_post_id',
            'sort_order' => 'asc',
            'head' => array('Title', 'Description', 'Moderate'),
            'columns' => array(array(
                'name' => 'Title',
                'index' => 'qa_title',
                'width' => 400
              ),array(
                'name' => 'Description',
                'index' => 'qa_description',
                'width' => 550
              ),array(
                'name' => 'Moderate',
                'sortable' => false,
                'width' => 196
              )
            )
          ));
        ?>
</div>
</div>
</div>

<?php else : ?>

<div style="padding-top: 20px;" >
Search name of the store, you want to moderate.<br/>
<strong>Note:</strong> Stores having moderation type set to "None", "Automatic" <?php echo (!$this->is_admin) ? 'and "Staff" ' : '' ?>will not be shown in suggestions

</div>
<?php endif; ?>

<div class="clearfix"></div>