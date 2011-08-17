
<?php use_javascript('tiny_mce/jquery.tinymce.js'); ?>
<?php use_javascript('settings/question'); ?>

<script type="text/javascript">
  var controller_action = 'answer';
</script>

<?php echo $this->load->view('components/_settingsSlider', array('tray_selected_thumb' => "answer"), true); ?>

<div class="lft_widget">
  <div class="content_dashboard">
    
    <div class="heading_section  clearfix">
      <div class="head">Answer Light Box</div>
      <!--input type="button" value="" class="btn_save fr mt10"-->
    </div>
    
    <div class="content_accordian">      
      <div class="disp_widget">
        <div>
          <div class="pop_row clearfix">
            <div class="lft_pop_head"></div>
            <div class="pop_head_rpt clearfix"><a href="#"><img src="<?php echo base_url() ?>images/frontend/btn_close.png" alt="Close" title="Close" width="16" height="15" /></a></div>
            <div class="rgt_pop_head"></div>												
          </div>
        </div>
        <div class="content_pop">
          <div>
            <span class="editable">            
              <div class="heading_edit"><span class="editable-text"><?php echo isset($this->row["title"])?$this->row["title"]:"Answer"?></span><a href="javascript:;" ><img rel="title" class="editable-link" src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="absmiddle" /></a></div>
              <div class="qtn_pan"><span class="editable-text"><?php echo isset($this->row["sub_title"])?$this->row["sub_title"]:"#Question"?></span><a href="javascript:;" ><img class="editable-link" rel="sub_title" src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="absmiddle" /></a></div>
          </div>
          <div class="text_are"><textarea class="tinymce" cols="20" rows="20"></textarea></div>

          <div id="auto-items-panel" <?php echo ((isset($products) && $products == "on") || (isset($categories) && $categories == "on") || (isset($brands) && $brands == "on")) ? '' : 'style="display:none"'?> >
          
            <div class="add_panel clearfix">          
              <div class="lbl_add">Add</div>
              <select id="options">        
                <?php if ($products == "on"): ?>
                  <option value="products"> Products</option>
                <?php endif; ?>        
                <?php if ($categories == "on"): ?>
                  <option value="categories"> Categories</option>
                <?php endif; ?>
                <?php if ($brands == "on"): ?>
                  <option value="brands"> Brands</option>
                <?php endif; ?>        
              </select>
              <input type="text" id="add_link" name="sub_cat" value="" /> <label style="display: none" id="no_record">No Records Found</label>
              <input type="button" class="btn_add_link fr" value="">								
            </div>

            <div class="browse_panel">
              <div class="add_panel clearfix">
                <div class="lbl_add">Browse </div>
                <select id="options_browse" class="add">        
                  <?php if ($products == "on"): ?>
                    <option value="products"> Products</option>
                  <?php endif; ?>        
                  <?php if ($categories == "on"): ?>
                    <option value="categories"> Categories</option>
                  <?php endif; ?>
                  <?php if ($brands == "on"): ?>
                    <option value="brands"> Brands</option>
                  <?php endif; ?>        
                </select>
                <div class="lbl_add">by name</div>
                <select id="options_name" onchange="populateCategory()" class="add">
                  <option value="none">Select</option>
                  <?php for ($i = 65; $i < 91; $i++): ?>
                    <option value="<?php echo chr($i) ?>"><?php echo chr($i) ?></option>
                  <?php endfor; ?>                           
                </select>
              </div>
              <div id="posts"></div>

            </div>
            
            <div class="clear"></div>
          
          </div>
            
          <div class="clear"></div>
        </div>
        
        <div class="white_btm_row clearfix">
          <div class="lft_pop_btm"></div>
          <div class="pop_btm_rpt"></div>
          <div class="rgt_pop_btm"></div>														
        </div>
      </div>
    </div>
    
  </div>
</div>

<div class="rgt_widget">
  <div class="content_dashboard">
    
    <div class="heading_section  clearfix">
      <div class="head">Function Options</div>
    </div>
    
    <div class="content_accordian">
      <div class="inner_function">
        <div class="avat_panel">
          <h3>Tags</h3>
          <?php foreach($this->custom_config['tags'] as $value):?>
              <label class="clearfix"><span class="avatat_tag"><?php echo $value?></span></label>
          <?php endforeach;?>
        </div>
        <h3>Linking</h3>
        <div class="row_function clearfix">
          <div class="list_item"><a href="#">Products:</a></div>
          <div class="list_on_off">
            <ul>
              <li class="frst"><a href="javascript:;" class="function-state <?php echo (isset($products) && $products == 'on') ? 'function-on' : '' ?>" rel="products">On</a></li>
              <li><a href="javascript:;" class="function-state <?php echo (isset($products) && $products == 'off') ? 'function-on' : '' ?>" rel="products">Off</a></li>										
            </ul>
          </div>
        </div>
        <div class="row_function clearfix">
          <div class="list_item"><a href="#">Categories:</a></div>
          <div class="list_on_off">
            <ul>
              <li class="frst"><a href="javascript:;" class="function-state <?php echo (isset($categories) && $categories == 'on') ? 'function-on' : '' ?>" rel="categories">On</a></li>
              <li><a href="javascript:;" class="function-state <?php echo (isset($categories) && $categories == 'off') ? 'function-on' : '' ?>" rel="categories">Off</a></li>										
            </ul>
          </div>
        </div>        
        <div class="row_function clearfix">
          <div class="list_item"><a href="#">Brands:</a></div>
          <div class="list_on_off">
            <ul>
              <li class="frst"><a href="javascript:;" class="function-state <?php echo (isset($brands) && $brands == 'on') ? 'function-on' : '' ?>" rel="brands">On</a></li>
              <li><a href="javascript:;" class="function-state <?php echo (isset($brands) && $brands == 'off') ? 'function-on' : '' ?>" rel="brands">Off</a></li>										
            </ul>
          </div>
        </div>

        <input type="hidden" name="edit_item" id="edit_item" value="0" />
        
      </div>

    </div>
  </div>
</div>

<div class="clear"></div>
<input type="hidden" id="save_edit_url" value="<?php echo $this->custom_config['email_save_edit_url']?>" />
<?php echo edit_dialog()?>