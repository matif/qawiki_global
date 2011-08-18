
<script type="text/javascript">
  
$(document).ready(function(e) {
  $("#desigFrm").validate({
    messages:"This field is required"
  });

  $("#designation").blur(function(){
    doAjax('post', "<?php echo base_url()?>teammembers/checkDesignation/<?php echo $this->store_id?>/"+$("#designation").val(), null, 'html', function(data){
      if(data == -1) {
        $("#err_doublicate").show();
      } else {
        $("#err_doublicate").hide();
      }
    });
  });
  
  $('.delete-designation').live('click', function(){
    var c = confirm('Are you sure, you want to delete this moderation group?');
    if(c){
      var self = this;
      doAjax('post', base_url+'teammembers/deleteDesignation/'+store_id+"/"+$(this).attr('rel'), null, 'html', function(){
        $(self).parent().parent().remove();
      })
    }
  });

  $('.edit-designation').live('click', function(){
    var self = this;
    var id = $(this).attr('rel');
    var $tr = $(self).parent().parent();
    if(!$tr.next().hasClass('edit-designation-tr')){
      doAjax('get', base_url+'teammembers/getDesignationInfo/'+store_id+'/'+id, null, 'html', function(data){
        $tr.after('<tr class="edit-designation-tr" id="edit_'+id+'"><td>&nbsp;</td><td colspan="6" style="padding:10px 0">'+data+'</td></tr>');
      });
    } else {
      $tr.next().remove();      
    }
  });
});

function save_edit_designation(element)
{
  var id = $(element).attr('rel');
  var $form = $(element).parent().parent();
  var designation = $form.find('input[name=designation]').val();
  var role = $form.find('select[name=role]').val();
  if(designation.replace(/\s+/, '') != ''){
    if($form){
      doAjax('post', base_url+'teammembers/saveEditDesignation/'+store_id+'/'+id, {"designation": designation,"role":role}, 'html', function(data){
        var tds = $('#edit_'+id).prev().find('td');
        $(tds[1]).html(designation);
        $(tds[2]).html(role);
        $('#edit_'+id).slideUp();
        $('#edit_'+id).remove();
      });
    }
  }
}
</script>

<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Manage Moderation Groups</div>    
  </div>
  
  <div class="content_accordian">
    <div class="disp_content_white" style="padding: 0">
      
      <table cellspacing="0" cellpadding="0" border="0" class="rpt_area">
        
        <tr>
          <th width="24">&nbsp;</th>
          <th>Name</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
        
        <?php foreach ($designations as $designation):?>
        
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center"><?php echo $designation['designation_name']?></td>
            <td align="center"><?php echo $designation['role']?></td>
            <td>
              <?php if ($role != "view"): ?>
                <a href="javascript:;" class="edit-designation" rel="<?php echo $designation['id']?>">Edit</a> | 
                <a href="javascript:;" class="delete-designation" rel="<?php echo $designation['id']?>">Delete</a>
              <?php else:?>
                -/-
             <?php endif;?>
            </td>
            </tr>           
          
        <?php endforeach;?>
        
        <tr>
          <?php if ($role != "view"): ?>
            <td>&nbsp;</td>
            <td colspan="6"><a href="javascript:;" id="add-designation-link" onclick="$('#add-new-designation').toggle()">+ Add New Moderation Group</a></td>
         <?php endif;?>
        </tr>
          
      </table>
      
    </div>
  </div>
</div>

<div id="add-new-designation" style="display:none">

  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head setting">Add Moderation Group</div>
    </div>
    
    <form action="<?php echo base_url(). 'teammembers/designations/'.$this->store_id ?>"  method="post" id="desigFrm" class="form-cont">
    
      <div class="content_accordian">
        <div id="content_1" class="disp_content_white">

          <div class="row_dat">
          </div>
          <div class="row_dat">
            <div class="lbel">Name:</div>
            <div class="lbl_inpuCnt" style="width:auto">            
              <input type="text" class="account_med required" id="designation" name="designation" value="<?php echo (isset($store_info[0]->qa_store_name) ? $store_info[0]->qa_store_name : '') ?>" style="margin-right: 10px"/><span style="display: none" id="err_doublicate" class="error">This designation is already created for the this store</span>
            </div>
            <div class="clear"></div>
          </div>
          
          <div class="row_dat">
            <div class="lbel" >Role:</div>
            <div class="lbl_inpuCnt" style="width:auto">
              <select class="fr wid_160px" name="role">
                <option value="admin">Admin</option>
                <option value="view">View</option>                
              </select>
            </div>
            <div class="clear"></div>
          </div>
          

          <div class="row_dat">
            <div class="lbel">&nbsp;</div>
            <input type="submit" class=" btn_save" tabindex="8" value="" name="edit_seller_info">
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </form>
  </div>

</div>