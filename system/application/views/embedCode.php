<script type="text/javascript">
 $(document).ready(function(){
   $("#simpleCodeOpt").attr("checked","true");
 })
  
</script>

<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Embed Code</div>
  </div>

  <div class="content_accordian">
    <div class="disp_content_white">
      
      <div class="row_dat">
        <div class="lbel">Embed Code:</div>
        <div class="lbl_inpuCnt" style="width:auto"> 

          <label class="radio-cont clearfix">
            <input type="radio" name="type" id = "simpleCodeOpt" value="simple" checked="true"  onchange="$('#simpleCode').show();$('#encodedCode').hide();" />
            <span class="avatat_tag">Simple</span>
          </label>

          <div id="simpleCode" class="emcode-code-cont">

            <textarea class="textarea embed-code"><?php echo htmlentities($embed_code) ?></textarea>

          </div>

          <label class="radio-cont clearfix">
            <input type="radio" name="type" value="encoded" onchange="$('#simpleCode').hide();$('#encodedCode').show();" />
            <span class="avatat_tag">Encoded</span>
          </label>

          <div id="encodedCode" class="emcode-code-cont" style="display: none">

            <textarea class="textarea embed-code"><?php echo htmlentities($encoded_code) ?></textarea>

          </div>

          <?php if (!trim($store->cart_type)) : ?>
          
            <div>

              <strong>Note:</strong> Replace {CUSTOMER_ID} with logged in customer id on your website

              <?php if (strpos($embed_code, '{CUSTOMER_EMAIL}') !== FALSE) : ?>
                , {CUSTOMER_EMAIL} with email of the customer 
              <?php endif; ?>

              <?php if (!trim($sub_id)) : ?>

                and {type} with product, category or brand, and {ID} with id of the {type}.

              <?php endif; ?>
                
            </div>

          <?php endif; ?>

        </div>
        <div class="clear"></div>
      </div>
      
    </div>
  </div>

</div>