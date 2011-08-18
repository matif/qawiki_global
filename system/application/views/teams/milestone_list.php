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
    
    $('#add-milestone-link').bind('click', function(){
      $('#add-new-milestone').toggle();
      var str = $(this).text();
      if(str.indexOf('+') > -1) {
        $(this).text(str.replace('+', '-'));
        $.get(base_url+'teams/getMilestoneInfo/<?php echo $this->store_id?>', function(data){
          $('#add-new-milestone').html(data);
        });
      } else {
        $(this).text(str.replace('-', '+'));
        $('#add-new-milestone').html('');
      }
    })
    
    $('.delete-milestone').bind('click', function(){
      var c = confirm('Are you sure, you want to delete this milestone?');
      if(c){
        var self = this;
        $.post(base_url+'teams/deleteMilestone/'+store_id+"/"+$(this).attr('rel'), function(){
          $(self).parent().parent().remove();
        })
      }
    });
    
    $('.edit-milestone').bind('click', function(){
      var self = this;
      var $tr = $(self).parent().parent();
      if(!$tr.next().hasClass('edit-milestone-tr')){
        $.get(base_url+'teams/getMilestoneInfo/'+<?php echo $this->store_id?>+'/'+$(this).attr('rel'), function(data){
          $tr.after('<tr class="edit-milestone-tr"><td>&nbsp;</td><td colspan="6" style="padding:10px 0">'+data+'</td></tr>');
        });
      } else {
        $tr.next().remove();
      }
    });
  });
</script>
<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Manage Milestones</div>
    <div class=""><a href="javascript:;"></a></div>
  </div>
  
  <div class="content_accordian">
    <div class="disp_content_white" style="padding: 0">
      
      <table cellspacing="0" cellpadding="0" border="0" class="rpt_area">
        
        <tr>
          <th width="24">&nbsp;</th>          
          <th>Milestone Name</th>                    
          <th>Number of Questions</th>
          <th>Number of  Answers</th>
          <th>Number of Question Liked</th>
          <th>Number of Answer Liked</th>
          <th>Actions</th>
        </tr>        
        <?php foreach ($milestones as $milestone):?>
        
          <tr>
            <td align="center">&nbsp;</td>            
            <td align="center"><?php echo $milestone['name']?></td>            
            <td align="center"><?php echo $milestone['question']?></td>
            <td align="center"><?php echo $milestone['answer']?></td>
            <td align="center"><?php echo $milestone['question_liked']?></td>
            <td align="center"><?php echo $milestone['answer_liked']?></td>                        
            <td>              
              <?php if ($role != "view"): ?>     
                <a href="javascript:;" class="edit-milestone" rel="<?php echo $milestone['id']?>">Edit</a> | 
                <a href="javascript:;" class="delete-milestone" rel="<?php echo $milestone['id']?>">Delete</a>              
              <?php else:?>
                -/-
              <?php endif;?>
            </td>
            
          </tr>

        <?php endforeach;?>        
        <tr>
          <?php if ($role != "view"): ?>       
            <td>&nbsp;</td>
            <td colspan="6"><a href="javascript:;" id="add-milestone-link">+ Add New Milestone</a></td>
          <?php endif;?>
        </tr>
          
      </table>
      
    </div>
  </div>
  
  
  
</div>

<div id="add-new-milestone" style="display:none"></div>