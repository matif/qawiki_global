
<?php use_javascript('tiny_mce/jquery.tinymce.js'); ?>
<?php use_javascript('catalog/catalog') ?>
<h1 class="black head_title setting"><span class="fl">Catalog Settings</span></h1>
<div class="clear"></div>
<div class="row_dat mt10">

  <div style="width: 500px;" class="lbl_inpuCnt">
    <div class="lbl_add"> Catalog Feed: </div>
    <a class="button clearfix fl" href="javascript:;" id="check">
      <span class="lft_area"></span>
      <span class="rpt_content" >Import Catalog</span>
      <span class="rgt_area"></span>														
    </a>
    <a class="button clearfix fl ml5" href="javascript:;" onclick="window.location.reload()">
      <span class="lft_area"></span>
      <span class="rpt_content">Refresh</span>
      <span class="rgt_area"></span>														
    </a>
  </div>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<br>

<div class="content_dashboard noborder">

  <div class="cate_pan" id="productsPanel">
    <div class="heading_section  clearfix"><div class="head nopad" style="width:auto; text-transform:capitalize;"><?php echo $items[0]['item_type']; ?></div></div>
    <div class="content_accordian">
      
      <div id="productPag_data">

        <?php echo (isset($items)) ? $this->load->view('catalog/_items') : '' ?>
        
      </div>

      <div class="heading_section clearfix">
        <div class="paginition_area clearfix productPag_pagin" style="padding-top:11px">

          <?php 
            echo (isset($items)) ? $this->load->view('components/_pagination', array_merge($items_params, array(
                'page_element_id' => 'productPag'
              )
            )) : '';
          ?>
        </div>					
      </div>

    </div>
  </div>
  
</div>

<input type="hidden" id="productPag_url" value="<?php echo base_url() . 'catalog/products/' . $this->store_id ?>" />

<div class="pop_container" id="questionDlg" style="display: none"></div>

<input type="hidden" id="ftp_name" value = "" />

<div id = "upload-csv-dlg" style="display: none">
  <div class="dlg-content">
    <form action = "" method = "" enctype="multipart/form-data">
      <div class = "dlg-row">
        <div class="lbel">
          <label class="radio-cont" style="display:block">
            <span class="avatat_tag">Upload CSV: </span>
          </label>
         </div>
          <div id="button-custom-img" class="lbel">
            <div class="lbl_inpuCnt" style = "padding-bottom:10px;">
              <input type = "file" name="upload_csv" id="upload_csv"/>
            </div>
          </div>
        <div class="clear"></div>
      </div>     
      
      <div class="dlg-row">
        <a id="sumbit_csv" class="button clearfix fl" href="javascript:;">
          <span class="lft_area"></span>
          <span class="rpt_content">Upload CSV</span>
          <span class="rgt_area"></span>
        </a>
      </div>
    </form>
  </div>
</div>