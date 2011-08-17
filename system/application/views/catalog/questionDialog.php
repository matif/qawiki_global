
  <div class="btm_content_pop">
    
    <form action="" id="questionForm">
    
      <div class="heading_edit">Question</div>
      <!--p class="ques">Question: </p-->
      <div class="msg_box" id="questionSaved" style="display: none">Your Question has been saved. <div class="clse"><a href="javascript:;" onclick="$('#questionSaved').hide()"></a></div></div>
      <div class="content_pan_pp">
        <div class="editor">
          <textarea class="tinymce silent" name="question-text" id="question-text"></textarea>
        </div>
      </div>
      <a class="button clearfix btn_sve_pop" href="javascript:;" onclick="save_question();">
        <span class="lft_area"></span>
        <span class="rpt_content">Save</span>
        <span class="rgt_area"></span>														
      </a>
      <div class="clear"></div>

      <div class="add_panel clearfix">
        <div class="lbl_add">Add</div>
        <select class="add" id="selectSug">
          <option value="category">Category</option>
            <option value="brand">Brand</option>
            <option value="product">Product</option>
        </select>
        
        <input type="text" id="sub_cat_dlg" name="sub_cat" value="">
        <input type="button" class="btn_add_link fr" value="" onclick="add_link_from_suggestion()">
        
        <input type="hidden" name="item_id" value="<?php echo $item_id?>" />
        <input type="hidden" name="item_type" value="<?php echo $item_type?>" />
        <input type="hidden" id="add_link_to" value="question-text" />
      </div>

    </form>

    <div class="browse_panel">
      <div class="add_panel clearfix">
        <div class="lbl_add">Browse </div>
        <select class="add" id="browseItem" onchange="get_items_by_name()">
          <option value="">Select</option>
          <option value="category">Categories</option>
          <option value="brand">Brands</option>
          <option value="product">Products</option>
        </select>
        <div class="lbl_add">by name</div>									
        <select class="add" id="browseName" onchange="get_items_by_name()">
          <option value="">Select</option>
          
          <?php for($i = 65; $i<91; $i++):?>
          
            <option value="<?php echo chr($i)?>"><?php echo chr($i)?></option>
          
          <?php endfor;?>  
            
        </select>
      </div>
      
      <div id="listItemsDialog"></div>
      
    </div>

    <div id="similarPost"></div>

  </div>
  <div class="btm_history clearfix">
    <div class="btm_left"></div>
    <div class="btm_rpt_history"></div>
    <div class="btm_right"></div>																
  </div>
  
<script type="text/javascript">

  attach_autocomplete(null, null, 'ajax/getStorePosts/'+store_id, 'sub_cat_dlg', 'selectSug', 'post', 'question_dialog_suggestion');

  attach_tinymce('question-text', '132px');

</script>