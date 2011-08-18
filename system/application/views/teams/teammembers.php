<?php echo use_javascript('invite'); ?>

<script type="text/javascript">
  $(document).ready(function()
  {  
    attach_pagination_events();
  
    $('#add_m').bind('click', function()
    {      
      id = $('.teamId').val();
      $("#team_id").val(id);
      
      if($("#add_member").css("display") == 'none')
      {        
        $("#add_member").show();
        $("#sign").text("-");
      }
      else
      {
        $("#add_member").hide();
        $("#sign").text("+");
      }
    });
    
    $('.add-badge-link').bind('click', function(){      
      $('#add-new-badge').toggle();      
      var str = $(this).text();
      $.get(base_url+'teams/getMilestoneBadgeInfo/'+<?php echo $this->store_id ?>, function(data){                  
        $('#add-new-badge').html(data);
      });      
    }) 
    
    $('.edit-memeber').live('click', function()
    {      
      $parent = $(this).parent().parent();
      $container = $parent.next();
      if($container && $container.attr('class') == 'team-memeber-box') {
        $container.slideUp("fast");
        $container.remove();
      }else{        
        makeEditMemeberBox($parent, this);
      }
    });
 
});
   
  
function remove_frame(element_id)
{
$('#'+element_id).parent().parent().remove();

}
  
function makeEditMemeberBox(parent, self)
{
var rel = $(self).attr('rel');
rel = rel.split(':|:');
var rel = $(self).attr('rel');
rel = rel.split(':|:');    
html = '<iframe src="'+base_url + 'teammembers/edit/'+ rel[0]+'/'+rel[1]+'/no_type/'+'<?php echo $this->store_id ?>'+'" id = "frame_'+rel[0]+'" width="100%" height="275" scrolling="no" frameborder="0">\
            <p>Your browser does not support iframes.</p>\
            </iframe>';
            $container = $('<tr class="team-memeber-box"></tr>');
            $container.html("<td colspan='5'>"+html+"</td>");
            $(parent).after($container);
            $container.find('.store-container').slideDown("fast");        
    
          }

</script>
<div class="content_dashboard">
  <div class="heading_section  clearfix">
    <div class="head setting">Team Settings</div>
  </div>

  <div id="teamPag_data">
    <?php echo $this->load->view("partials/_teammembers", array("teammembers" => $teammembers), true) ?>
  </div>

  <div>
    <div class="heading_section clearfix">
      <div class="paginition_area clearfix teamPag_pagin" style="padding-top:11px">

        <?php
        echo $this->load->view('components/_pagination', array_merge($team_params, array(
                    'page_element_id' => 'teamPag'
                        )
                ));
        ?>
      </div>
    </div>
  </div>
</div>

<div id="add-new-badge" style="display:none"></div>

<div class="header_rgt" style ="display: none" id ="add_member"  >
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head setting">Add New Member</div>
    </div>
    <div id="confirmation" class="success" style="display:none">The invitation is send successfully</div>
    <div id="error" class="error" style="display:none">The invitation is already sent to this email</div>

    <form class="constrain" method="" action="" id="addFrm">
      <div class="content_accordian">
        <div class="disp_content_white">
          <input type="hidden" value="1" id="count_rows" name="count_rows">
          <div class="lbel" style="padding-left: 5px">Add Member </div>
          <div style="width: 500px;" class="lbl_inpuCnt">
            <input type="text" value="" class="account_med fl" style="margin:3px 10px 0 0; width: 207px; height: 16" id="user_email" name="user_email" value="Type Your text Here" onclick="this.value=''" onblur="(this.value == ''? this.value ='Type Your text Here':'')" >
            &nbsp;<a class="button clearfix fl" href="javascript:;">
              <span class="lft_area"></span>
              <span class="rpt_content" id="addBtn">Send</span>
              <span class="rgt_area"></span>														
            </a><span style="display: none" id="no_record">No Record Found</span><label class="error" id="error_email" style="display: none" >Invalid Email address</label></div>
          <div class="clear"></div>
        </div>
      </div>

      <div>
        <input type="hidden" value="" id="team_id" name="teamId" />
      </div>
    </form>
    <div class="clear"></div>
  </div>
  <input type="hidden" id="teamPag_url" value="<?php echo base_url() . 'teammembers/get_more_members/' . $team_id . '/' . $this->store_id ?>" />
</div>
