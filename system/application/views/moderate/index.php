
<?php use_javascript('tiny_mce/jquery.tinymce.js'); ?>
<?php use_javascript('moderate_new'); ?>

<div class="content_dashboard noborder ">
  <div class="cate_pan edit_td">
    <div class="heading_section heading_moderate_mod clearfix"><div class="head moderate" style="width:auto">List Options</div></div>
    <div class="content_accordian inner_moderate">
      <div class="filter_area clearfix">
        <div class="fl">					
          <div class="search_tag" style="width:50px; line-height:20px">Search</div>
          <input type="text" name="txt_filter_search" class="select_text" id="searchbox">
        </div>
        <div class="fr">					
          <div class="search_tag" style="line-height:20px">Select Date Range</div>
          <input type="text" id="start_date" name="start_date" class="select_text_date" />
          <input type="text" id="end_date" name="end_date" class="select_text_date" />								
        </div>
      </div>
      <div class="clear"></div>
      <div class="links items-filter" <?php echo ($item_id) ? 'style="display:none"' : ''?>>
        <ul>
          <li class="<?php echo ($item_id && $item_type != 'product') ? 'collapse' : 'expand'?>" rel="product"><a href="javascript:;">Product</a></li>
          <li class="<?php echo (!$item_id) ? 'expand' : 'collapse'?>" rel="widget"><a href="javascript:;">Contributor</a></li>
          <!--li class="expand"><a href="javascript:;">Moderator Level</a></li-->
          <li class="<?php echo ($item_id && $item_type != 'brand') ? 'collapse' : 'expand'?>" rel="brand"><a href="javascript:;">Brand</a></li>
          <li class="<?php echo ($item_id && $item_type != 'category') ? 'collapse' : 'expand'?>" rel="category"><a href="javascript:;">Category</a></li>
          <!--li class="collapse"><a href="javascript:;">Group</a></li-->
        </ul>
      </div>
      <div class="clear"></div>					
      <div class="links power_bar clearfix" style="display:none">
        <ul>
          <!--li class="collapse"><a href="javascript:;">Group</a></li>
          <li class="collapse"><a href="javascript:;">Group</a></li-->
        </ul>
      </div>

    </div>
  </div>
</div>		
<div class="clear"></div>
<div class="content_dashboard noborder ">
  <div class="cate_pan">
    <div class="heading_section clearfix">
      <div class="head moderate" style="width:auto">Sort Moderate list by</div> 
      <select name="sort-options" id="sort-options" class="mod_select" style="width: 270px" onchange="reload_data()">
        <option value="helpful">Questions With the Most Helpful Answers</option>
        <option value="recentq">Most Recent Questions</option>
        <option value="oldestq">Oldest Questions</option>
        <option value="recenta">Questions With Most Recent Answers</option>
        <option value="oldesta">Questions With Oldest Answers</option>
        <option value="answers">Questions With Most Answers</option>
        <option value="noanswers">Can You Answer These Questions?</option>
      </select>

      <div class="mod_pag">
        <div class="paginition_area clearfix pagination_pagin">

          <?php echo $this->load->view('components/_pagination', $params); ?>

        </div>
      </div>

    </div>
    <div class="content_accordian" style="margin:0;">
      <div class="header_mod clearfix">
        <ul>
          <li>Bulk Actions:</li>
          <li><a href="javascript:;" onclick="select_all(true)">Select All</a></li>
          <li><a href="javascript:;" onclick="select_all(false)">Select None</a></li>
          <li><a href="javascript:;" onclick="change_all_mod_status('valid')">Approve</a></li>
          <li><a href="javascript:;" onclick="change_all_mod_status('invalid')">Reject</a></li>
          <li><a href="javascript:;" onclick="export_all_questions()" >Export</a></li>
        </ul>
      </div>
      <div class="inner_moderate">
        <ul class="inner_moderate_listing" id="pagination_data">

          <?php echo $this->load->view('moderate/_postList', array("designations" => $designations), true); ?>

        </ul>
      </div>
      <div class="clear"></div>							
    </div>

    <div class="heading_section clearfix">
      <div class="paginition_area clearfix pagination_pagin" style="padding-top:11px">
        <?php echo $this->load->view('components/_pagination', $params); ?>
      </div>					
    </div>
  </div>
</div>

<input type="hidden" id="pagination_url" name="pagination_url" value="<?php echo base_url() . 'moderate/paginate/' . $this->store_id ?>" />
<input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id?>" />
<input type="hidden" id="item_type" name="item_type" value="<?php echo $item_type?>" />

<div class="pop_container" id="answerDlg" style="display: none"></div>

<div class="pop_container" id="emailDlg" style="display: none">
  
  <p>Email will be send on these email addresses you can add more comma separated.</p>
  <textarea id="send_email" name="send_email"></textarea>
  <div class="popup-btns">
    <div style="display: none" id="emailSent" class="msg_box">Email has been sent. <div class="clse"><a onclick="$('#emailSent').hide()" href="javascript:;"></a></div></div>
    <div style="display: none" id="emailError" class="error_box"><span></span> <div class="clse"><a onclick="$('#emailError').hide()" href="javascript:;"></a></div></div>
    
    <input type="button" class="btn" value="Send" onclick="send_email();" rel="0" id="btnSend" />
    <input type="button" class="btn" value="Cancel" onclick="hideJModalDialog('emailDlg')" />
  </div>
  <div class="clear"></div>
</div>

<div id="dlg_export" class="" style="display: none">
  <div class="dlg-content">
    <div class = "row_dat">
      <div class="lbel"><strong>Export Q&A:</strong></div>
      <div class="export_excel lbl_inpuCnt" style = "padding-left:60px;">
        <a href="<?php echo base_url().'moderate/export/'.$this->store_id.'/'?>" rel ="xls" id="export_xls">Download CSV</a>
      </div>
      <div class="clear"></div>
    </div>
    
    <div class = "row_dat">
      <div style = "padding-left:60px;" class="export_html lbl_inpuCnt">
        <a href="<?php echo base_url().'moderate/export/'.$this->store_id.'/'?>" rel ="html" id="export_html">Download HTML</a>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>