
<?php use_javascript('settings/email'); ?>
<?php use_javascript('colorpicker'); ?>

<?php echo link_tag('css/colorpicker.css'); ?>
<?php echo link_tag($this->widget_css_file); ?>

<script type="text/javascript">
  var store_url = '<?php echo get_store_dir_url($this->store_id)?>';
</script>

<?php echo $this->load->view('components/_settingsSlider', array('tray_selected_thumb' => "contributor"), true); ?>

<script type="text/javascript">
  $(document).ready(function(){
    $(".custom, .none").click(function(){        
      $(this).parent().parent().find('.default_options').slideUp("fast");
      if($(this).hasClass('none'))
        $("#"+$(this).attr("rel")).attr("disabled",true);
      else
        $("#"+$(this).attr("rel")).attr("disabled",false);
    });
    $(".default").click(function(){
      $("#"+$(this).attr("rel")).attr("disabled",true);
      $(this).parent().parent().find('.default_options').slideDown("fast");
    });
  });
</script>

<div class="lft_widget">  
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head">Contributor Lightbox Preview</div>      
    </div>
    <div class="content_accordian">
      <div class="disp_widget">
        <div class="pop_row clearfix">
          <div class="lft_pop_head"></div>
          <div class="pop_head_rpt clearfix"><a href="#"><img src="<?php echo base_url() ?>images/frontend/btn_close.png" alt="Close" title="Close" width="16" height="15" /></a></div>
          <div class="rgt_pop_head"></div>												
        </div>
        <div class="content_pop">          
          <?php echo widget_popup_banner((isset($appearance["header"]) ? $appearance["header"] : array()), 'qaw-contributor-header');?>
          
          <div class="user-detail clearfix">
            <div class="qaw-user-avatar"></div>
            <div class="heading_edit no_bdr"><span class="editable-text"><?php echo isset($row["title"]) ? $row["title"] : "Dear #UserName" ?></span>, <a href="javascript:;"><img width="16" rel="title" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>
            <div class="dt_pan">
              <ul>
                <li><a href="javascript:;">#JoinDate</a></li>
                <li><a href="javascript:;">#VoteScore</a></li>
              </ul>
            </div>
          </div>
          <div class="question_panel">
            <h3>Questions</h3>
            <ul>
              <li>Is this a good question? <span class="editable-text"><?php echo isset($appearance["date_question"]) ? $appearance["date_question"] : "#Date/Time" ?> </span> <a href="javascript:;"><img width="16" rel="date_question" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></li>
              <li>Is this also a super great question? <span class="editable-text"><?php echo isset($appearance["date_super"]) ? $appearance["date_super"] : "#Date/Time" ?></span> <a href="javascript:;"><img width="16" rel="date_super" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></li>
            </ul>
          </div>						
          <div class="question_panel">
            <h3>Answers</h3>
            <ul>
              <li>This a good answer. <span class="editable-text"> <?php echo isset($appearance["date_answer"]) ? $appearance["date_answer"] : "#Date/Time" ?> </span> <a href="javascript:;"><img width="16" rel="date_answer" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></li>
              <li>Is this also a super great answer. <span class="editable-text"> <?php echo isset($appearance["date_super_answer"]) ? $appearance["date_super_answer"] : "#Date/Time" ?> </span> <a href="javascript:;"><img width="16" rel="date_super_answer" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></li>
            </ul>
          </div>
          
          <div>
            <?php echo widget_button('contributor', $this->custom_config['default_button'], 'close', $this->custom_config['buttons']['contributor'], $appearance);?>
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

  <form action="<?php echo base_url() ?>settings/contributor_functions/<?php echo $this->store_id ?>/<?php echo $this->uri->segment(2) ?>" method="post" id="form_data" enctype="multipart/form-data" >
    <div class="content_dashboard">
      <div class="heading_section  clearfix">
        <div class="head">Appearance Options</div>
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
            
            <h3>Avatar Image</h3>
            <label class="clearfix">
              <input type="radio" rel="avatar_image" name="avatar_option" value="default" <?php echo isset($appearance["avatar"]['option']) && $appearance["avatar"]['option'] == "default" ? "checked = 'true'" : "checked = 'true'" ?> class="default" />
              <span class="avatat_tag">Default</span>
            </label>
            
            <div style="margin-left:18px;<?php echo ((isset($appearance['avatar']['option']) && $appearance['avatar']['option'] == 'default') || !isset($appearance['avatar']['option'])) ?"": 'display:none'?>" class="default_options">
              <!--div class="row_dat">
                <div class="lbel">Text:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance["avatar"]['text']) ? $appearance["avatar"]['text'] : "" ?>" id="avatar_img_cont" name="avatar_text" />
                </div>
                <div class="clear"></div>
              </div-->
              <div class="row_dat">
                <div class="lbel">Color:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance["avatar"]['color']) ? $appearance["avatar"]['color'] : "FF0000" ?>" id="font_color" name="avatar_color" />
                </div>
                <div class="clear"></div>
              </div>
            </div>
            
            <label class="clearfix">
              <input type="radio" rel="avatar_image" name="avatar_option" value="none" <?php echo isset($appearance['avatar']['option']) && $appearance['avatar']['option'] == "none" ? "checked = 'true'" : "" ?> class="none" />
              <span class="avatat_tag">None</span>
            </label>
            
            <label class="clearfix">
              <input type="radio" rel="avatar_image" name="avatar_option" value="custom" class="custom" <?php echo isset($appearance['avatar']['option']) && $appearance['avatar']['option'] == "custom" ? "checked = 'true'" : "" ?>/>
              <span class="avatat_tag">Custom 32x32px</span>
            </label>
            
            <div class="sho_hid clearfix">
              <input type="file" name="avatar_image" <?php echo isset($appearance["avatar"]["image"]) && $appearance['avatar']['option'] == "custom" ? "" : "disabled='true'" ?> id="avatar_image"/>
              
                <?php if (isset($appearance["avatar"]["image"]) && trim($appearance["avatar"]["image"]) && $appearance["avatar"]["option"] == "custom"): ?>
              
                  <img src="<?php echo get_image_path($appearance["avatar"]["image"], $this->store_id) ?>" alt="" title="" align="top" />
                  
                <?php endif; ?>
                  
            </div>
          </div>

          <div class="avat_panel" style="padding-bottom:0">
            
            <h3>Header Image</h3>
            <label class="clearfix">
              <input type="radio" rel="header_image" name="header_option" value="default" class="default" <?php echo isset($appearance["header"]['option']) && $appearance["header"]['option'] == "default" ? "checked = 'true'" : "checked = 'true'" ?> />
              <span class="avatat_tag">Default</span>
            </label>
            
            <div style="margin-left:18px;<?php echo ((isset($appearance['header']['option']) && $appearance['header']['option'] == 'default') || !isset($appearance['header']['option'])) ?"": 'display:none'?>"  class="default_options">
              <div class="row_dat">
                <div class="lbel">Text:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance['header']['text']) ? $appearance['header']['text'] : "" ?>" id="header_img_cont" name="header_text" />
                </div>
                <div class="clear"></div>
              </div>
              <div class="row_dat">
                <div class="lbel">Font Color:</div>
                <div class="lbl_inpuCnt">
                  <input type="text" class="input-fld" value="<?php echo isset($appearance['header']['color']) ? $appearance['header']['color'] : "000000" ?>" id="font_color" name="header_color" />
                </div>
                <div class="clear"></div>
              </div>
            </div>
            
            <label class="clearfix">
              <input type="radio" rel="header_image" name="header_option" value="none" <?php echo isset($appearance['header']['option']) && $appearance['header']['option'] == "none" ? "checked = 'true'" : "" ?> class="none"/>
              <span class="avatat_tag">None</span>
            </label>
            
            <label class="clearfix">
              <input type="radio" rel="header_image" name="header_option" value="custom" <?php echo isset($appearance['header']['option']) && $appearance['header']['option'] == "custom" ? "checked = 'true'" : "" ?> class="custom"/>
              <span class="avatat_tag">Custom <?php echo $this->widget_width?>x40px</span>
            </label>
            
            <div class="sho_hid clearfix">
              <input type="file" name="header_image" id="header_image" <?php echo isset($appearance['header']['image']) && $appearance['header']['option'] == "custom" ? "" : 'disabled="true"' ?> />
              
              <?php if (isset($appearance['header']['image']) && trim($appearance['header']['image']) && $appearance['header']['option'] == "custom"): ?>
              
                <img src="<?php echo get_image_path($appearance['header']['image'], $this->store_id) ?>" alt="" title="" align="top" />
                
              <?php endif; ?>
                
              <input type="submit" value="" class="btn_save fr mt10" />
            </div>
          </div>

        </div>
      </div>
    </div>
  </form>
</div>

<input type="hidden" id="save_edit_url" value="<?php echo $this->custom_config['email_save_edit_url'] ?>" />

<div class="clear"></div>

<?php echo edit_dialog()?>
<?php echo edit_button_dialog('contributor') ?>
