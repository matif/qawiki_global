
<script type="text/javascript">
  $(document).ready(function(){
    $("#custom").click(function(){    
      $("#custom_pic").attr("disabled",false);
      $("#default_view").slideUp("fast");
    });
  
    $("#default").click(function(){
      $("#custom_pic").attr("disabled",true);  
      $("#default_view").slideDown("fast");
    });
  
    $("#none").click(function(){
      $("#custom_pic").attr("disabled",true);
      $("#default_view").slideUp("fast");
    });    
  
  });
</script>


<div class="rgt_widget">
  <?php
  if (isset($error)):
    echo "<div class = 'error'>" . $error . "</div>";
  endif;
  ?>
  <form action="<?php echo base_url() ?>settings/functions_options/<?php echo $this->store_id ?>/<?php echo $this->uri->segment(2) ?>" method="post" id="form_data" enctype="multipart/form-data" >
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
       
          <div class="avat_panel">
            <h3>Banner Image</h3>            
            <label class="clearfix"><input type="radio" name="picture" value="default" id="default" <?php echo (isset($appearance['image']) && $appearance['image'] == "default") ? "checked = 'true'" : "checked = 'true'" ?> /><span class="avatat_tag">Default</span></label>
            <div style="margin-left:18px;<?php echo ((isset($appearance['image']) && $appearance['image'] == 'default') || !isset($appearance['image'])) ? "" : 'display:none'?>" id="default_view">              
              <div class="row_dat">
                <div class="lbel">Text:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance["default_text"]) ? $appearance["default_text"] : "" ?>" id="" name="default_text" />
                </div>
                <div class="clear"></div>
              </div>
              <div class="row_dat">
                <div class="lbel">Font Color:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance["font_color"]) ? $appearance["font_color"] : "000000" ?>" id="font_color" name="font_color" />
                </div>
                <div class="clear"></div>
              </div>
            </div>
            <label class="clearfix"><input type="radio" name="picture" <?php echo (isset($appearance['image']) && $appearance['image'] == "none") ? "checked = 'true'" : "" ?> value="none" id="none" /><span class="avatat_tag">None</span></label>            
            <label class="clearfix"><input type="radio" name="picture" id="custom" value="custom" <?php echo (isset($appearance['image']) && $appearance['image'] == "custom") ? "checked = 'true'" : "" ?> /><span class="avatat_tag">Custom <?php echo $this->widget_width ?>x40px</span></label>
            <div class="sho_hid clearfix">
              <input type="file" name="custom_pic" id="custom_pic"  <?php echo (isset($appearance['image_name']) && $appearance['image'] == "custom" ? "" : "disabled = 'true'") ?>/>
              <?php if (isset($appearance['image_name']) && $appearance['image_name'] != false && $appearance['image'] == "custom"): ?>
                <img src="<?php echo get_image_path($appearance['image_name'], $this->store_id) ?>" alt="" title="" align="top" />
                <input type ="hidden" name="edit_image" value ="<?php echo $appearance['image_name'] ?>"/>
              <?php endif; ?>
            </div>
          </div>
          <input type="hidden" name ="width" value="<?php echo isset($this->apparance["width"]) ? $this->apparance["width"] : "16" ?>" />
          <div class="avat_panel" style="padding-bottom:0">
            
          <?php if (isset($view) && $view != "thankyou"): ?>
            
            <h3>Email</h3>            
            <label class="clearfix"><span class="avatat_tag">From Email Name</span></label>
            <input type="text" name="email" value="<?php echo (isset($appearance['email_name']) ? $appearance['email_name'] : "") ?>" class="email5" />
            <label class="clearfix"><span class="avatat_tag">From/Reply to Email Address</span></label>
            <input type="text" name="from"  class="email5" id="from" value="<?php echo (isset($appearance['from']) ? $appearance['from'] : "") ?>"/>
            
          <?php endif; ?>
              
            <input type="submit" id="save" value="" class="btn_save fr mt10">
          </div>

          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </form>
</div>