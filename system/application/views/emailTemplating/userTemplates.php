<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<?php echo grid_libraries()?>
<script type="text/javascript" src="<?php echo base_url() ?>js/admin/email_template.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/admin/main.js"></script>
<script type="text/javascript">
var template_types = "";
var store_id = <?php echo $this->store_id; ?>;
</script>

<form id="add-form" method="post" action="" onsubmit="return save_template('user');" class="constrain"  >
  <h2 class="">Add Thanks Template</h2>
  <div>
    <label class="top" for="tx_content">Content:</label>
    <textarea name="content" id="tx_content" class="required tinymce">
    <?php echo (isset($template) && is_array($template) && count($template) > 0)? $template[0]["content"] : ''?>
    </textarea>
  </div>
  <input type="hidden" name="email_type" value="thank_you" />
  <div>
    <label>&nbsp;</label>
    <input type="submit" value="Save" />
    <input type="hidden" value="0" name="template_id" id="template_id" />
  </div>
</form>