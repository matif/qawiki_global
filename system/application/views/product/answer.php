<?php echo link_js('products');?>
<?php  echo link_js('link_products');?>

<script type="text/javascript">
  var is_iframe_version = true;
  var pord_cnt = <?php echo isset($this->product_count)?$this->product_count: 0 ?>;    
  
</script>

<?php if(isset($this->error) && trim($this->error)):?>
  <div class="error"><?php echo $this->error?></div>
<?php endif;?>

<form action="<?php echo base_url()?>post/addQuestion/<?php echo $this->store_id?>/answer/<?php echo $this->type?>/<?php echo $this->ref?>/<?php echo $this->parent_id?>/<?php echo $this->post_id?>/<?php echo isset($post)?1:0?>" class="constrain" method="post" enctype="multipart/form-data">

  <div>
    <label>Title </label> <input type="text" name="title" id="title" value="<?php echo isset($post->qa_title)? $post->qa_title: ''?>"/>
    <span id="err_title" class="error" style="display: none">This field is required</span>
  </div>
  
  <div>
    <label class="top">Description </label> <textarea cols="25" rows="5" name="description" id="description"><?php echo isset($post->qa_description)? $post->qa_description: ''?></textarea>
    <span id="err_description" class="error" style="display: none">This field is required</span>
  </div>
<label class="top">Related Products</label>

    <div class="">
      <?php if(isset($this->products)):?>
      <?php foreach($this->products as $product):?>
        <div class="edit-products">
          <div class="remove">REMOVE</div>
            <div class="linked-prod">
              <table cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                  <td><?php echo $product['title']?></td>
                  </tr>
                </tbody>
              </table>
            </div>

        </div>
      <?php endforeach;?>
      <?php endif;?>
      <div>
        <?php echo ((isset($this->product_count) && $this->product_count < 3)|| !isset($this->product_count)) ?'<div class="link-products add-related-link">':'<div class="link-products add-related-link" style = "display:none ">'?>
        <input type="hidden" value="<?php echo isset($product['post_id'])?$product['post_id']:''?>" name="qawiki_products[]">
          <div class="add">ADD</div>
          <div class="linked-prod search-icon"></div>
        </div>
      </div>

      <div class="clear"></div>
    </div>
  <input type="hidden" value="<?php echo isset($product['post_id'])?$product['post_id']:''?>" name="products[]">
  <div>
    <label class="top">Video Caption </label> <input type = "text" name="video_caption" id = "video_caption" value="<?php echo isset($post->video_caption)? $post->video_caption: ''?>"/>
    <span id="err_caption" class="error" style="display: none">This field is required</span>
  </div>

  <div>
    <label class="top">Video URL </label> <input type = "text" name="video_url" id = "video_url" value="<?php echo isset($post->video_url)? $post->video_url: ''?>"/><br>
    <label>&nbsp;</label><span>(Paste the URL of your video from YouTube)</span><br>
    <span id="err_caption" class="error" style="display: none">This field is required</span>
  </div>

  <?php if($this->imaageOptions == 3 || $this->imaageOptions == 4 ):?>
  <div>
    <label class="top">Upload an Image </label> <input type = "file" name="image" id = "image"/><?php echo (isset($post->image_url)?  "<img src = '".$post->image_url."'alt = 'NO IMAGE FOUND'/>":"")?>
    <span id="err_caption" class="error" style="display: none">This field is required</span>
  </div>
  <?php endif; ?>

  <div>
    <label>&nbsp;</label>
    <input type="submit" id="postAnswer" class="postAnswer" value="Post"  />
    <label class="error" id="language" style="display:none">Bad language used </label>
    <input type="hidden" name="reference_id" id="reference_id" value="0" />
    <input type="hidden" name="post_id" id="post_id" value="0" />
    <input type="hidden" name="reference_type" id="reference_type" value="0" />
  </div>
</form>
 <div id="search" ></div>
  <div id = "qawiki-popup-content"></div>
  <div id="qaPopup_Desc"></div>