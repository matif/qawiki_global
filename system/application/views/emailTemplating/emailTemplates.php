<script type="text/javascript" src="<?php echo base_url() ?>js/admin/email_template.js"></script>

<script type="text/javascript" src="<?php echo base_url() ?>js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/admin/main.js"></script>

<script type="text/javascript">
  var template_types = <?php echo json_encode($template_type) ?>;
  $(document).ready(function(){
    $('.delete').live('click', function(){
      var c = confirm('Are you sure, you want to delete this template?');
      if(c) {
        var self = this;
        var template_id = $(self).attr('rel');
        $.post(base_url + 'emailTemplates/deleteTemplate', {template_id : template_id}, function(response){
          template_types.push($(self).parent().prev().text().toLowerCase());
          $(self).parent().parent().remove();
          if(edit_id == template_id) {
            $('#add-form').hide();
          }
          $('#add-new').show();
        });
      }
    });
  });
</script>


<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Manage Email Templetes</div>
    <div class=""><a href="javascript:;"></a></div>
  </div>

  <table cellpadding="0" cellspacing="0" id="data-list"></table>
  <div id="pager"></div>
  <div class="content_accordian">
    <div class="disp_content_white" style="padding: 0">

      <table cellspacing="0" cellpadding="0" border="0" class="rpt_area">       
        <tr>
          <th width="24">&nbsp;</th>          
          <th>Template Id</th>                    
          <th>Contents</th>
          <th>Email Type</th>         
          <th>Actions</th>
        </tr> 

        <?php foreach ($emailtempletes as $res): ?>        
          <tr>
            <td align="center">&nbsp;</td>            
            <td align="center"><?php echo $res['id'] ?></td>            
            <td align="center"><?php echo $res['content'] ?></td>
            <td align="center"><?php echo $res['type'] ?></td>          
            <td>              
              <a href="javascript:;" class="edit-record" rel="<?php echo $res['id'] ?>">Edit</a> | 
              <a href="javascript:;" class="delete" rel="<?php echo $res['id'] ?>">Delete</a>              
            </td>
          </tr>
        <?php endforeach; ?>

        <tr>
          <td>&nbsp;</td>
          <td colspan="6"><a href="javascript:;" id="add-new" style="<?php echo (!empty($template_type)) ?>">+ Add New</a>
            <form id="add-form" action="" onsubmit="return save_template('user');" class="constrain" style="display: none">
              <h2 class="">Add Email Template</h2>
              <div>
                <label class="top" for="tx_content">Content:</label>
                <textarea name="content" id="tx_content" class="required tinymce"></textarea>
              </div>

              <div>
                <label class="top" for="email_type">Type:</label>
                <select name="email_type" id="email_type" class="required">

                  <?php foreach ($template_type as $value) : ?>

                    <option value="<?php echo $value ?>"><?php echo ucfirst($value) ?></option>

                  <?php endforeach; ?>

                </select> &nbsp; <input type="submit" value="Save" />
              </div>

              <input type="hidden" value="0" name="template_id" id="template_id" />

            </form>          
          </td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td colspan="6">
            <div class="placeholder">
              <a href="javascript:;" class="email-guide">Placeholder</a>
              <p style="display: none">                                
                <span>{QUESTION} - title of the question</span><br />
                <span>{URL} - url of the widget</span><br />
                <span>{USER_NAME} - name of the user to whom email will be sent</span><br />
                <span>{ITEM_TITLE} - title of the product, category or brand</span><br />
                <span>{BASE_URL} - url of the qawiki panel</span><br />
                <span>{TURN_OFF_NOTIF} - Stop receiving answers to this question</span>                                
              </p>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>

</div>