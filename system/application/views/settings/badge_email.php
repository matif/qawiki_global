
<?php use_javascript('settings/email');?>
<?php use_javascript('colorpicker');?>

<?php echo link_tag('css/colorpicker.css'); ?>
<?php echo link_tag($this->widget_css_file); ?>

<?php echo $this->load->view('components/_settingsSlider', array('tray_selected_thumb' => "badge_email"), true); ?>

<div class="lft_widget">
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head">Badge Email Preview</div>      
      <input type="button" value="" class="btn_save fr mt10">
    </div>
    <div class="content_accordian">
      <div class="disp_widget">
        <div class="pop_row pop_row_big clearfix">
          <div class="lft_pop_head"></div>
          <div class="pop_head_rpt clearfix">
            <table cellpadding="0" cellspacing="0" border="0" class="fl">
              <tr>
                <td valign="middle" height="77">
                  <?php echo widget_popup_banner($appearance, 'qaw-badge-email-header');?>
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
        </div>
        <div class="content_pop">
          <div class="heading_edit no_bdr"><span class="editable-text"><?php echo isset($row["title"])?$row["title"]:"Dear #UserName"?></span>, <a href="javascript:;"><img width="16" class="editable-link" rel ="title" height="16" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>
          <div class="qtn_pan normal_text"><span class="editable-text"><?php echo isset($row["sub_title"])?$row["sub_title"]:"You have added new badge"?></span>.<a href="javascript:;" ><img width="16" rel="sub_title" height="16" class="editable-link" align="absmiddle" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>
          <div class="qtn_pan normal_text">From,<br /><span class="editable-text"><?php echo isset($row["from_email"])?$row["from_email"]:"Q&amp;A Support Team"?></span><a href="javascript:;"><img width="16" rel ="from_email" height="16" align="absmiddle" class="editable-link" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a></div>						
          <div class="email_footer">
            <span class="editable-text"><p>This email was sent to #email by #QAEmail </p>
              <?php echo isset($row["email_footer"])?$row["email_footer"]:'
              <ul>
                <li class="nobg">Update <a href="#">Profile/Email Address</a></li>
                <li>Instant removal with <a href="#">SafeUnsubscribe</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li>#QA_Address</li>																											
              </ul>'?>;              
              </span>
            <a href="javascript:;"><img width="16" rel ="email_footer" height="16" align="absmiddle" class="editable-link" title="Ico Edit" alt="Ico Edit" src="<?php echo base_url() ?>images/frontend/ico_edit.png"></a>
          </div>
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

<?php echo $this->load->view("settings/functions_option",array("view"=>"badge"), true)?>
<input type="hidden" id="save_edit_url" value="<?php echo $this->custom_config['email_save_edit_url']?>" />
<div class="clear"></div>

<?php echo edit_dialog()?>