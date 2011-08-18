<?php
/*   To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type="text/javascript">
  function selectImage(element)
  {
    var html = "";
    if($(element).attr('class')!='selectImage')
    {
      $(element).addClass('selectImage');    
      $parent = $(element).parent();
      $.each($parent.find('.selectImage'), function(i,e){            
        rel = $(e).attr('rel');           
        html +='<input type="hidden" name="image_url[]" value="'+rel+'" />';     
      
      });      
      $("#url_image").html(html);
    }
    else
    {
      $(element).removeClass('selectImage');      
    }
  }
  
</script>
<?php if ($this->error == 1): ?>
  <div class="error">Please input a valid image file</div>
<?php endif; ?>
<form action="<?php echo base_url() ?>teammembers/edit/<?php echo $this->team_id ?>/<?php echo $this->id ?>/submit" class="constrain" method="post" enctype="multipart/form-data">
  
  
  <div class="row_dat">
    <div style="width:190px" class="lbel">Moderation Groups </div>
    <div class="lbl_inpuCnt" style="width:auto">
      <select name ="designation">        
        <?php if(is_array($designations)):?>
          <?php foreach($designations as $des):?>
          <option value='<?php echo $des["designation_name"];?>' <?php echo (isset($data[0]) && trim($data[0]->designation) && $data[0]->designation == $des["designation_name"]) ? 'selected="selected"' : '' ?>><?php echo $des["designation_name"]?></option>
          <?php endforeach;?>
        <?php endif;?>
      </select>
    </div>
    <div class="clear"></div>
  </div>
  
  <div class="row_dat">
    <div style="width:190px" class="lbel">Select Your Badge</div>
    <div class="lbl_inpuCnt" style="width:auto">
      <input type ="file" id ="badge" name="badge"/>
    </div>
    <div class="clear"></div>
  </div>
  
  <div id="badges" style="padding-top: 10px; padding-bottom: 10px;">    
    <a href="javascript:;" onclick="selectImage(this)" rel="default_1.gif" <?php echo isset($image[0]) ? check_image('default_1.gif', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_1.gif" alt="" width="50px" height="50" /></a>
    <a href="javascript:;" onclick="selectImage(this)" rel="default_2.gif" <?php echo isset($image[0]) ? check_image('default_2.gif', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_2.gif" alt="" width="50px" height="50" /></a>
    <a href="javascript:;" onclick="selectImage(this)" rel="default_3.jpg" <?php echo isset($image[0]) ? check_image('default_3.jpg', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_3.jpg" alt="" width="50px" height="50" /></a>
    <a href="javascript:;" onclick="selectImage(this)" rel="default_4.jpg" <?php echo isset($image[0]) ? check_image('default_4.jpg', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_4.jpg" alt="" width="50px" height="50" /></a>
    <a href="javascript:;" onclick="selectImage(this)" rel="default_5.jpg" <?php echo isset($image[0]) ? check_image('default_5.jpg', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_5.jpg" alt="" width="50px" height="50" /></a>
    <a href="javascript:;" onclick="selectImage(this)" rel="default_6.jpg" <?php echo isset($image[0]) ? check_image('default_6.jpg', $image) : "" ?>><img src = "<?php echo base_url() ?>images/badges/default_6.jpg" alt="" width="50px" height="50" /></a>

    <?php foreach ($badges as $badge): ?>
      <?php if ($data[0]->qa_team_id == $badge['qa_team_id']): ?>
        <a href="#" onclick="selectImage(this)" rel="<?php echo $badge['image_url'] ?>"<?php echo (isset($data[0]) && trim($data[0]->image_url) && $data[0]->image_url == $badge['image_url']) ? "class = 'selectImage'" : "" ?>><img src ="<?php echo base_url() ?>uploads/teams/<?php echo $data[0]->qa_team_id ?>/t-<?php echo $badge['image_url'] ?>" width="50px" height="50"/></a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <div class="row_dat">
    <div style="width:190px" class="lbel">Notify Me On Comment</div>
    <div class="lbl_inpuCnt" style="width:auto">
      <input type="checkbox" name = "notify_me_on_comment" <?php echo (isset($data[0]) && trim($data[0]->notify_me_on_comment) && $data[0]->notify_me_on_comment == 1) ? 'checked="checked"' : '' ?> value="on"/>
    </div>
    <div class="clear"></div>
  </div>
  <div class="row_dat">
    <div style="width:190px" class="lbel">Notify Me On Vote</label></div>
    <div class="lbl_inpuCnt" style="width:auto"><input type="checkbox" name = "notify_me_on_vote" <?php echo (isset($data[0]) && $data[0]->notify_me_on_vote == 1 ) ? 'checked="checked"' : '' ?> value="on"/>
    </div>
    <div class="clear"></div>
  </div>
  <div id="url_image">      
  </div>
  <div>
<!--    <input type="submit" value="Update Member" id="sumbit-member"  />-->
    <input type="submit" class=" btn_save" tabindex="8" value="" name="edit_seller_info">
  </div>
</form>
