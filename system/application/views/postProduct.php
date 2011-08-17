
<script type="text/javascript">
  $(document).ready(function(e) {
    $("#postFrm").validate({
      rules:
      {

      },
      messages:"This field is required",
      submitHandler: function(form)
      {
        var retVal = true;
        if($('#file').val()=='')
        {
          $('#filePath').text('Enter the file name');
          $('#filePath').show();
          retVal = false;
        }
        else
          $('#filePath').hide();

        var ext = $('#file').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['csv']) == -1) {
          $('#filePath').text('Please upload a valid file');
          $('#filePath').show();
          retVal = false;
        }
        else
          $('#filePath').hide();
                

        if(!retVal)
          return retVal;

        form.submit();
      }
    });
  });
   
  
</script>
<div class="content_dashboard">
  
  <div class="heading_section  clearfix">
    <div class="head mc">Import Catalog</div>
  </div>
  
  <form action="<?php echo base_url()?>index.php/post/formatPost/<?php echo $this->store_id?>/post" method="post" enctype="multipart/form-data" id="postFrm" class="constrain">

    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">

        <div class="row_dat">
          <label><strong>Upload CSV file</strong></label>
        </div>
        
        <div class="row_dat">
          <div class="error" id="filePath" <?php echo isset($error) ? '' : 'style="display:none"'?>><?php echo isset($error) ? $error : ''?></div>
        </div>

        <div class="row_dat">
          <div class="lbel">File Name:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <input type="file" name="file" id="file" class="account_med" />
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div class="lbel">&nbsp;</div>
          Sample format of csv (product_code,title,description)<br/>Please click <a target="_blank" href="<?php echo base_url().'files/' ?>postsdata.csv">here</a> to view sample CSV file.
          <div class="clear"></div>
        </div>
        <? if($this->role !="creator"):?>
        <div class="row_dat">
          <div class="lbel">Products Association:</div>
          <div class="lbl_inpuCnt" style="width:auto">
            <?php
              $CI =& get_instance();
              $cols  = $CI->config->item('csv_fields');
              $cols["product title"] = $cols["title"];
              $cols["product description"] = $cols["description"];
              unset($cols ["image url"]);
              unset($cols ["title"]);
              unset($cols ["description"]);
              unset($cols ["product url"]);
              unset($cols ["parent id"]);
            ?>
            <select name="association" class="required">
              <?php
              foreach($cols as $key => $value):                
                echo "<option value ='qa_".str_replace(" ", "_", $key) ."'>".$value."</option>";
              endforeach;
              ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>
       <?php endif;?>
    
        <div>
          <div class="submit">
            <input type="submit" value="" id="submit" class="btn_save btn_new" />
          </div>
        </div>
      </div>
    </div>        
  </form>  
</div>