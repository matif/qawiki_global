

<style type="text/css">
  #badge-form div{margin-bottom: 10px}
  #badge-form label{display: inline-block; width: 130px}
  #badge-form  label.error{margin-left: 10px; width: auto}
  .badges-list{border-collapse: collapse; border-color: #fff; width: 100%}
  .badges-list td, .badges-list th{padding: 3px 5px; border-color: #d2d2d2}
</style>

<script type="text/javascript">
  $(document).ready(function(){
    $('#badge-form').validate({
      rules: {
        badge_image: {
          required: true,
          accept: "png|jpg|jpeg|pjpg|pjpeg|gif"
        }
      }
    });
    
    $('#add-badge-link').bind('click', function(){
      $('#add-new-badge').toggle();
      var str = $(this).text();
      if(str.indexOf('+') > -1) {
        $(this).text(str.replace('+', '-'));
        $.get(base_url+'teams/getMilestoneBadgeInfo/'+store_id, function(data){
          $('#add-new-badge').html(data);
        });
      } else {
        $(this).text(str.replace('-', '+'));
        $('#add-new-badge').html('');
      }
    })
    
    $('.delete-badge').bind('click', function(){
      var c = confirm('Are you sure, you want to delete this badge?');
      if(c){
        var self = this;
        $.post(base_url+'teams/deleteMilestoneBadge/'+store_id, {badge_id: $(this).attr('rel')}, function(){
          $(self).parent().parent().remove();
        })
      }
    });
    
    $('.edit-badge').bind('click', function(){
      var self = this;
      var $tr = $(self).parent().parent();
      if(!$tr.next().hasClass('edit-badge-tr')){
        $.get(base_url+'teams/getMilestoneBadgeInfo/'+store_id+'/'+$(this).attr('rel'), function(data){
          $tr.after('<tr class="edit-badge-tr"><td>&nbsp;</td><td colspan="6" style="padding:10px 0">'+data+'</td></tr>');
        });
      } else {
        $tr.next().remove();
      }
    });
  });
</script>

<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Manage Badges</div>    
  </div>
  
  <div class="content_accordian">
    <div class="disp_content_white" style="padding: 0">
      
      <table cellspacing="0" cellpadding="0" border="0" class="rpt_area">
        
        <tr>
          <th width="24">&nbsp;</th>
          <th>Badge ID</th>
          <th>Badge Name</th>
          <th>Number Awarded</th>
          <th>Milestone</th>
          <th>Badge Image</th>
          <th>Actions</th>
        </tr>       
        <?php foreach ($badges as $badge):?>
        
          <tr>
            <td>&nbsp;</td>
            <td><?php echo $badge['id']?></td>
            <td><?php echo $badge['badge_name']?></td>
            <td><?php echo $badge['numbers_awarded']?></td>
            <td><?php echo $badge['name']?></td>
            <td><img src="<?php echo base_url().'uploads/'.$store_id.'/custom_badges/t-'.$badge['badge_image']?>" /></td>
            <td>
              <?php if ($role != "view"): ?>
                <a href="javascript:;" class="edit-badge" rel="<?php echo $badge['id']?>">Edit</a> | 
                <a href="javascript:;" class="delete-badge" rel="<?php echo $badge['id']?>">Delete</a>
              <?php else:?>
                -/-
              <?php endif;?>
            </td>
          </tr>

        <?php endforeach;?>
      
        <tr>
          <?php if ($role != "view"): ?>
            <td>&nbsp;</td>
            <td colspan="6"><a href="javascript:;" id="add-badge-link">+ Add New Badge</a></td>
          <?php endif;?>
        </tr>
          
      </table>
      
    </div>
  </div>
</div>

<div id="add-new-badge" style="display:none">

</div>