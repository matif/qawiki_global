

<?php use_javascript('advanced_report')?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>css/jquery/jquery-ui.custom.css" />

<script type="text/javascript">
  var $picker_container = null;
</script>

<form action="<?php echo base_url().'advancedReport/generate/'.$store_id?>" method="post" id="report-form">

  <div style="float:left">
    <div class="row_dat">
      <div class="lbel">From:</div>
      <div class="lbl_inpuCnt">
        <input type="text" id="start_date" class="account_med" name="start_date" />
      </div>
      <div class="clear"></div>
    </div>

    <div class="row_dat">
      <div class="lbel">To:</div>
      <div class="lbl_inpuCnt">
        <input type="text" id="end_date" class="account_med" name="end_date" />
      </div>
      <div class="clear"></div>
    </div>

    <div style="padding-bottom: 15px">
      <a id="generate-report" class="qaw-buton-gray qaw-button"><span>Generate Report</span></a>
      <div class="clear"></div>
    </div>
  </div>
  
  <div>
    <a style="float:right" class="qaw-buton-gray qaw-button" href="<?php echo base_url() . 'reports/index/' . $store_id ?>"><span>View Report</span></a>
  </div>
  
  <div class="clear"></div>
  
  <?php
    echo $this->load->view('components/_moveItems', array(
      'title'        => 'Categories',
      'items'        => $categories,
      'item_field'   => 'qa_category_name',
      'move_id'      => 'mv_category',
      'sub_heading'  => 'Category',
      'picker'       => true,
      'post_field'   => 'categories',
      'picker_route' => base_url().'ajax/categoryPicker/'.$store_id
    ));
  ?>

  <?php
    echo $this->load->view('components/_moveItems', array(
      'title'        => 'Brands',
      'items'        => $brands,
      'item_field'   => 'qa_brand_name',
      'move_id'      => 'mv_brand',
      'sub_heading'  => 'Brand',
      'picker'       => true,
      'post_field'   => 'brands',
      'picker_route' => base_url().'ajax/brandPicker/'.$store_id
    ));
  ?>

  <?php
    echo $this->load->view('components/_moveItems', array(
      'title'        => 'Fields',
      'items'        => $this->config->item('report_fields'),
      'item_field'   => 'title',
      'move_id'      => 'mv_fields',
      'sub_heading'  => 'Field',
      'picker'       => false,
      'post_field'   => 'fields'
    ));
  ?>

</form>