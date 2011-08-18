
<?php use_javascript('settings/email');?>
<?php use_javascript('colorpicker');?>

<?php echo link_tag('css/colorpicker.css'); ?>
<?php echo link_tag($this->widget_css_file); ?>

<script type="text/javascript">
  var store_url = '<?php echo get_store_dir_url($this->store_id)?>';
</script>

<?php echo $this->load->view('components/_settingsSlider', array('tray_selected_thumb' => "thank_you"), true); ?>

<div class="lft_widget">
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head">Than You Page Preview</div>        
    </div>
    <div class="content_accordian">
      <div class="disp_widget">
        <div class="pop_row clearfix">
          <div class="lft_pop_head"></div>
          <div class="pop_head_rpt clearfix"><a href="#"><img src="<?php echo base_url()?>images/frontend/btn_close.png" alt="Close" title="Close" width="16" height="15" /></a></div>
          <div class="rgt_pop_head"></div>												
        </div>
<!--        <div class="pop_row pop_row_big clearfix">
          <div class="lft_pop_head"></div>
          <div class="pop_head_rpt clearfix">
            <table cellpadding="0" cellspacing="0" width="175" border="0" class="fl">
              <tr>
                <td align="center" valign="middle" height="77">
                  <img height="30px" src="<?php echo isset($appearance['image_name']) ? get_image_path($appearance['image_name'], $this->store_id):base_url()."images/frontend/logo_gotbody.png"?>" alt="" title="" align="top" />                  
                </td>                
              </tr>
            </table>
            <table cellpadding="0" cellspacing="0" width="175" border="0" class="fr">
              <tr>
                <td align="right" valign="middle" height="77" class="qa_18"><a href="#">Q&amp;A </a></td>
              </tr>
            </table>
          </div>
          <div class="rgt_pop_head"></div>												
        </div>-->
        <div class="content_pop">
          
          <?php echo widget_popup_banner($appearance, 'qaw-thank-you-header');?>
          
          <div class="heading_edit no_bdr"><span class="editable-text"><?php echo isset($row["title"])?$row["title"]:"Dear #UserName"?></span>, <a href="javascript:;"><img width="16" class="editable-link" rel ="title" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>
          <div class="qtn_pan normal_text"><span class="editable-text"><?php echo isset($row["sub_title"])?$row["sub_title"]:"Thank you for answering and growing community."?></span><a href="javascript:;"><img rel ="sub_title" width="16" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>
           <div class="question_panel">
           <span class="editable-text">
            <?php echo isset($row["email_footer"])?$row["email_footer"]:
            '<h3>Thank You for answer and growing community!!</h3>
            <ul>
              <li>Your answer will be post after review</li>              
            </ul>'?>
            </span>
           <a href="javascript:;"><img rel ="email_footer" width="16" class="editable-link" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a>
          </div>	
          <div class="qtn_pan normal_text">From,<br /><span class="editable-text"><?php echo isset($row["from_email"])?$row["from_email"]:"Q&amp;A Support Team"?></span><a href="javascript:;"><img width="16" rel ="from_email" height="16" align="absmiddle" class="editable-link" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>						
          <div class="email_footer">
            <?php echo widget_button('thank_you', $this->custom_config['default_button'], 'No Thanks Take back to the product page', $this->custom_config['buttons']['thank_you'], $appearance);?>
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

<?php echo $this->load->view("settings/functions_option",array("view" => "thankyou"), true)?>

<input type="hidden" id="save_edit_url" value="<?php echo $this->custom_config['email_save_edit_url']?>" />

<div class="clear"></div>

<?php echo edit_dialog()?>
<?php echo edit_button_dialog('thank_you') ?>