
<script type="text/javascript" src="<?php echo base_url() ?>js/admin/email_template.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/tiny_mce/jquery.tinymce.js"></script>

<script type="text/javascript">
  var template_types = <?php echo json_encode($template_type)?>;
</script>

<h1 class="page-heading">Email Templates</h1>

<div class="actions">
  <span class="button-link"><a href="javascript:;" id="add-new" style="<?php echo (!empty($template_type)) ? '' : 'display: none'?>">Add New</a></span>
</div>

<div class="placeholder">
  <a href="javascript:;" class="email-guide">Placeholder</a>
  <p style="display: none">
    <span>{QUESTION} - title of the question</span>
    <span>{URL} - url of the widget</span>
    <span>{USER_NAME} - name of the user to whom email will be sent</span>
    <span>{ITEM_TITLE} - title of the product, category or brand</span>
    <span>{BASE_URL} - url of the qawiki panel</span>
    <span>{TURN_OFF_NOTIF} - Stop receiving answers to this question</span>
  </p>
</div>

<table cellpadding="0" cellspacing="0" class="data-list">

  <thead>
    <tr>
      <th width="7%">Sr. #</th>
      <th>Content</th>
      <th width="10%">Email Type</th>
      <th width="15%">Actions</th>
    </tr>
  </thead>

  <tbody>

    <?php if($templates) :?>

      <?php foreach($templates as $key => $template) : ?>
        
        <tr valign="top">
          <td align="center"><?php echo $key + 1?></td>
          <td><?php echo htmlentities($template['content'])?></td>
          <td align="center"><?php echo ucfirst($template['type'])?></td>
          <td align="center">
            <a href="javascript:;" class="edit-record" rel="<?php echo $template['id']?>">Edit</a> |
            <a href="javascript:;" class="delete-record" rel="<?php echo $template['id']?>">Delete</a>
          </td>
        </tr>

      <?php endforeach;?>

    <?php else:?>

        <tr>
          <td colspan="4" align="center">No record found!</td>
        </tr>

    <?php endif;?>

  </tbody>

</table>

<form id="add-form" action="" onsubmit="return save_template();" class="constrain" style="display: none">
  <h2 class="heading">Add Email Template</h2>
  <div>
    <label class="top" for="tx_content">Content:</label>
    <textarea name="content" id="tx_content" class="required tinymce"></textarea>
  </div>

  <div>
    <label class="top" for="email_type">Type:</label>
    <select name="email_type" id="email_type" class="required">
      
      <?php foreach($template_type as $value) : ?>

        <option value="<?php echo $value?>"><?php echo ucfirst($value)?></option>

      <?php endforeach;?>

    </select>
  </div>

  <div>
    <label>&nbsp;</label>
    <input type="submit" value="Save" />
    <input type="hidden" value="0" name="template_id" id="template_id" />
  </div>
</form>