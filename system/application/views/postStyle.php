
<?php echo link_tag('css/colorpicker.css'); ?>

<script type="text/javascript" src="<?php echo base_url().'js/jquery-ui.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url().'js/colorpicker.js';?>"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#font_color , #link_color').ColorPicker({
      onSubmit: function(hsb, hex, rgb, el) {
      $(el).val(hex);
      $(el).ColorPickerHide();
      },
      onBeforeShow: function () {
        $(this).ColorPickerSetColor(this.value);
      },
      onShow: function (colpkr) {
        $(colpkr).fadeIn(300);
        return false;
      },
      onHide: function (colpkr) {
        $(colpkr).fadeOut(300);
        return false;
      },
      onChange: function (hsb, hex, rgb) {
        $(this).val(hex);
      }
    })
    .bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
    });
  });
</script>

<div class="content_dashboard">
  
  <div class="heading_section  clearfix">
    <div class="head sync">Customize Widget</div>
  </div>

  <form enctype="multipart/form-data" action="<?php echo base_url().'post/postStyle/'.$this->store_id.'/'.$type ?>" method="POST" id="productFrm" class="constrain">

    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">
    
        <?php if(!empty($error)) :?>

          <div class="row_dat">
            <div class="error"><?php echo $error['error']?></div>
          </div>

        <?php endif;?>
        
        <div class="row_dat">
          <strong>Size</strong>
        </div>

        <div class="row_dat">
          <div class="lbel">Width:</div>
          <div class="lbl_inpuCnt">
            <input type="text" class="account_med" value="<?php echo $width?>" id="width" name="width" />
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div class="lbel">Height:</div>
          <div class="lbl_inpuCnt">
            <input type="text" class="input-fld" value="<?php echo $height?>" id="height" name="height" />
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <strong>Colors</strong>
        </div>

        <div class="row_dat">
          <div class="lbel">Font Family:</div>
          <div class="lbl_inpuCnt">
            <?php echo select_tag('font_family', get_font_family_list(), $font_family, array('class' => 'input-fld'))?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div class="lbel">Font Color:</div>
          <div class="lbl_inpuCnt">
            <input type="text" class="input-fld" value="<?php echo $font_color?>" id="font_color" name="font_color" />
          </div>
          <div class="clear"></div>
        </div>  

        <div class="row_dat">
          <div class="lbel">Link Color:</div>
          <div class="lbl_inpuCnt">
            <input type="text" class="input-fld" value="<?php echo $link_color?>" id="link_color" name="link_color" />
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <strong>Logo</strong>
        </div>

        <div class="row_dat">
          <div class="lbel">Upload Logo</div>
          <div class="lbl_inpuCnt" style="width: auto">
            <input type="file" id="store_logo" name="store_logo" />

            <img src="<?php echo get_image_path($icon_path)?>" alt="" title="" align="top" />
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <input type="submit" value="" class="btn_save"/>
        </div>
        
      </div>
    </div>
    
  </form>

</div>