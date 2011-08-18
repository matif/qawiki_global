<?php echo link_tag('css/colorpicker.css'); ?>
<?php echo link_tag($this->widget_css_file); ?>

<?php use_javascript('colorpicker'); ?>
<?php use_javascript('settings/appearance'); ?>

<script type="text/javascript">
  var store_url = '<?php echo get_store_dir_url($this->store_id)?>';
</script>

<?php echo $this->load->view('components/_settingsSlider', array('tray_selected_thumb' => 'appearance'), true); ?>


<form enctype="multipart/form-data" action="<?php echo base_url() . 'settings/appearance/' . $this->store_id ?>" method="POST" id="productFrm" class="constrain">

  <div class="lft_widget">
    <div class="content_dashboard">

      <div class="heading_section  clearfix">
        <div class="head">Q&amp;A</div>
        <input type="submit" value="" class="btn_save fr mt10">
      </div>

      <div class="content_accordian">
        <div class="disp_widget">

          <div style="width: <?php echo ($width > 700) ? 700 : $width ?>px" class="widget-container qaw-text" id="qaw-widget">

            <div class="pop_row_widget clearfix">
              <div class="lft_pop_head"></div>
              <div class="pop_head_rpt clearfix"></div>
              <div class="rgt_pop_head"></div>												
            </div>
            <div class="clear"></div>            

            <div class="content_pop content_pop_gr">
              <div class="heading_edit">
                <span class="editable-text"><?php echo (isset($widget_configuration['title'])) ? $widget_configuration['title'] : '#Product Q&amp;A' ?></span> 
                <a href="javascript:;"><img src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="absmiddle" class="editable-link" rel="title" /></a>
              </div>
              
              <div>
                <?php echo widget_button('ask_question', $default_button, 'Question', $this->custom_config['buttons']['ask_question'], (isset($widget_configuration) ? $widget_configuration : null));?>
              </div>
              
              <div class="tab_section clearfix">
                <ul>
                  <li id="popular_tab_li" <?php echo ($popular_tab == 'on') ? '' : 'style="display:none"' ?> class="selected tab"  rel ="popular">
                    <a href="javascript:;">
                      <span class="editable-text"><?php echo (isset($widget_configuration['tabs']['popular_tab'])) ? $widget_configuration['tabs']['popular_tab'] : 'Popuplar' ?></span> 
                      <img src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="texttop" class="editable-link" rel="popular_tab" />
                    </a>
                  </li>

                  <li id="recent_tab_li" class="tab" <?php echo ($recent_tab == 'on') ? '' : 'style="display:none"' ?> rel ="recent">
                    <a href="javascript:;">
                      <span class="editable-text"><?php echo (isset($widget_configuration['tabs']['recent_tab'])) ? $widget_configuration['tabs']['recent_tab'] : 'Recent' ?></span>
                      <img src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="texttop" class="editable-link" rel="recent_tab" />
                    </a>
                  </li>

                  <li id="unanswered_tab_li" class="tab" <?php echo ($unanswered_tab == 'on') ? '' : 'style="display:none"' ?> rel ="unanswered">
                    <a href="javascript:;">
                      <span class="editable-text"><?php echo (isset($widget_configuration['tabs']['unanswered_tab'])) ? $widget_configuration['tabs']['unanswered_tab'] : 'Unanswered' ?></span> 
                      <img src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="texttop" class="editable-link" rel="unanswered_tab" />
                    </a>
                  </li>

                  <!--li id="search_tab_li" class="tab" <?php echo ($search_tab == 'on') ? '' : 'style="display:none"' ?> rel ="search">
                    <a href="javascript:;">
                      <span class="editable-text"><?php echo (isset($widget_configuration['tabs']['search_tab'])) ? $widget_configuration['tabs']['search_tab'] : 'Search' ?></span> 
                      <img src="<?php echo base_url() ?>images/frontend/ico_edit.png" alt="Ico Edit" title="Ico Edit" width="16" height="16" align="texttop" class="editable-link" rel="search_tab" />
                    </a>
                  </li-->
                </ul>                
                <div class="clear"></div>

                <div class="tab_content">
                  
                  <?php echo $this->load->view("settings/tabs_html", array( "type" => "popular"), true);?>
                  
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

  <div class="rgt_widget">

    <div class="content_dashboard">

      <div class="heading_section  clearfix">
        <div class="head">Appearance Options</div>
      </div>
      
      <div class="content_accordian">        
        <div id="content_1" class="disp_content_white">

          <div class="avat_panel">
            <h3>Tags</h3>
            <?php foreach($this->custom_config['tags'] as $value):?>
              <label class="clearfix"><span class="avatat_tag"><?php echo $value?></span></label>
            <?php endforeach;?>
          </div>
          
          <?php if (!empty($error)) : ?>

            <div class="row_dat">
              <div class="error"><?php echo $error['error'] ?></div>
            </div>

          <?php endif; ?>

          <div class="row_function clearfix">
            <h3>Size</h3>
          </div>

          <div class="row_dat">
            <div class="lbel">Width:</div>
            <div class="lbl_inpuCnt">
              <input type="text" class="account_med" value="<?php echo $width ?>" id="width" name="width" />
            </div>
            <div class="clear"></div>
          </div>

          <div class="row_dat">
            <div class="lbel">Height:</div>
            <div class="lbl_inpuCnt">
              <label class="radio-cont clearfix">
                <input type="radio" value="auto" name="height_opt" id="height_auto" <?php echo ($height_opt == 'auto') ? 'checked=""' : ''?> onchange="$('#height').hide();$('.widget-container').css({'height': 'auto', 'overflow': 'hidden'});" />
                <span class="avatat_tag">Auto</span>
              </label>
              
              <label class="radio-cont clearfix">
                <input type="radio" value="custom" name="height_opt" id="height_custom" <?php echo ($height_opt == 'custom') ? 'checked=""' : ''?> onchange="$('#height').show()" />
                <span class="avatat_tag">Custom</span>
              </label>
              
              <input type="text" class="input-fld" value="<?php echo $height ?>" id="height" name="height" style="margin-left: 18px; <?php echo ($height_opt == 'custom') ? '' : 'display: none'?>" />
            </div>
            <div class="clear"></div>
          </div>

          <div class="row_dat">
            <strong>Colors</strong>
          </div>

          <div class="row_dat">
            <div class="lbel">Font Family:</div>
            <div class="lbl_inpuCnt">
              <?php echo select_tag('font_family', get_font_family_list(), $font_family, array('class' => 'input-fld')) ?>
            </div>
            <div class="clear"></div>
          </div>

          <div class="row_dat">
            <div class="lbel">Font Color:</div>
            <div class="lbl_inpuCnt">
              <input type="text" class="input-fld" value="<?php echo $font_color ?>" id="font_color" name="font_color" />
            </div>
            <div class="clear"></div>
          </div>  

          <div class="row_dat">
            <div class="lbel">Link Color:</div>
            <div class="lbl_inpuCnt">
              <input type="text" class="input-fld" value="<?php echo $link_color ?>" id="link_color" name="link_color" />
            </div>
            <div class="clear"></div>
          </div>

          <div class="row_dat">
            <div class="lbel">Action Text Color:</div>
            <div class="lbl_inpuCnt">
              <input type="text" class="input-fld" value="<?php echo $action_text_color ?>" id="action_text_color" name="action_text_color" />
            </div>
            <div class="clear"></div>
          </div>

          <div class="row_dat">
            <strong>Logo</strong>
          </div>

          <div class="row_dat">
            <div class="lbel">Upload Logo</div>
            <div class="lbl_inpuCnt">
              <input type="file" id="store_logo" name="store_logo" />

              <img src="<?php echo get_image_path($icon_path, $this->store_id) ?>" alt="" title="" align="top" style="margin-top: 10px" />
            </div>
            <div class="clear"></div>
          </div>

        </div>
      </div>

    </div>

    <div class="content_dashboard">

      <div class="heading_section  clearfix">
        <div class="head">Function Options</div>
      </div>

      <div class="content_accordian">

        <div class="inner_function">

          <h3>Tabs</h3>

          <div class="row_function clearfix">
            <div class="list_item"><a href="javascript:;"><?php echo (isset($widget_configuration['tabs']['popular_tab'])) ? $widget_configuration['tabs']['popular_tab'] : 'Popuplar' ?>:</a></div>
            <div class="list_on_off">
              <ul>
                <li class="frst"><a href="javascript:;" <?php echo ($popular_tab == 'on') ? 'class="function-on"' : '' ?> rel="popular_tab">On</a></li>
                <li><a href="javascript:;" <?php echo ($popular_tab == 'off') ? 'class="function-on"' : '' ?> rel="popular_tab">Off</a></li>
              </ul>
            </div>
          </div>

          <div class="row_function clearfix">
            <div class="list_item"><a href="javascript:;"><?php echo (isset($widget_configuration['tabs']['recent_tab'])) ? $widget_configuration['tabs']['recent_tab'] : 'Recent' ?>:</a></div>
            <div class="list_on_off">
              <ul>
                <li class="frst"><a href="javascript:;" <?php echo ($recent_tab == 'on') ? 'class="function-on"' : '' ?> rel="recent_tab">On</a></li>
                <li><a href="javascript:;" <?php echo ($recent_tab == 'off') ? 'class="function-on"' : '' ?> rel="recent_tab">Off</a></li>
              </ul>
            </div>
          </div>

          <div class="row_function clearfix">
            <div class="list_item"><a href="javascript:;"><?php echo (isset($widget_configuration['tabs']['unanswered_tab'])) ? $widget_configuration['tabs']['unanswered_tab'] : 'Unanswered' ?>:</a></div>
            <div class="list_on_off">
              <ul>
                <li class="frst"><a href="javascript:;" <?php echo ($unanswered_tab == 'on') ? 'class="function-on"' : '' ?> rel="unanswered_tab">On</a></li>
                <li><a href="javascript:;" <?php echo ($unanswered_tab == 'off') ? 'class="function-on"' : '' ?> rel="unanswered_tab">Off</a></li>
              </ul>
            </div>
          </div>

          <!--div class="row_function clearfix">
            <div class="list_item"><a href="javascript:;"><?php echo (isset($widget_configuration['tabs']['search_tab'])) ? $widget_configuration['tabs']['search_tab'] : 'Search' ?>:</a></div>
            <div class="list_on_off">
              <ul>
                <li class="frst"><a href="javascript:;" <?php echo ($search_tab == 'on') ? 'class="function-on"' : '' ?> rel="search_tab">On</a></li>
                <li><a href="javascript:;" <?php echo ($search_tab == 'off') ? 'class="function-on"' : '' ?> rel="search_tab">Off</a></li>
              </ul>
            </div>
          </div-->

          <br/>

          <div class="row_function clearfix">
            <h3>Voting</h3>
          </div>

          <label class="clearfix">
            <input type="radio" name="voting_option" value="default" <?php echo ($voting_option == 'default') ? 'checked="checked"' : '' ?> />
            <span class="avatat_tag">Default</span>
          </label>

          <label class="clearfix">
            <input type="radio" name="voting_option" value="custom" <?php echo ($voting_option == 'custom') ? 'checked="checked"' : '' ?> />
            <span class="avatat_tag">Custom 16x16px</span>
          </label>

          <div class="clearfix sho_hid" style="padding-left:30px">
            <div>Positive Vote Image</div>
            <input type="file" name="pos_vote_image" value="" />

            <?php if (trim($vote_positive_image)): ?>

              <br/>
              <img src="<?php echo get_image_path($vote_positive_image, $this->store_id) ?>" alt="" title="" align="top" style="margin-top: 10px" />

            <?php endif; ?>
          </div>

          <div class="row_dat" style="padding-left:30px">
            <div>Negative Vote Image</div>
            <input type="file" name="neg_vote_image" value="" />

            <?php if (trim($vote_negative_image)): ?>

              <br/>
              <img src="<?php echo get_image_path($vote_negative_image, $this->store_id) ?>" alt="" title="" align="top" style="margin-top: 10px" />

            <?php endif; ?>
          </div>

          <input type="hidden" id="popular_tab" name="popular_tab" value="<?php echo $popular_tab?>" />
          <input type="hidden" id="recent_tab" name="recent_tab" value="<?php echo $recent_tab?>" />
          <input type="hidden" id="unanswered_tab" name="unanswered_tab" value="<?php echo $unanswered_tab?>" />
          <input type="hidden" id="save_edit_url" value="<?php echo $this->custom_config['appearance_save_edit_url'] ?>" />          

        </div>
      </div>

    </div>
  </div>

  <div class="clear"></div>

</form>

<div class="clear"></div>


<?php echo edit_dialog() ?>
<?php echo edit_button_dialog() ?>