<link rel="stylesheet" href="<?php echo base_url() . 'css/jquery/jquery-ui.custom.css' ?>" type="text/css" />
<?php echo link_js('ajaxfileupload'); ?>
<?php echo use_javascript('invite'); ?>
<script type="text/javascript">

  var manage_row = null;

  $(document).ready(function(e) {
    $('.edit-team').click(function(){
      $parent = $(this).parent().parent();
      $container = $parent.next();
      if($container && $container.attr('class') == 'team-box') {
        $container.slideUp("fast");
        $container.remove();
      } else {
        makeEditTeamBox($parent, this);
      }
    });
    $('.edit-memeber').live('click', function()
    {      
      $parent = $(this).parent().parent();
      $container = $parent.next();
      if($container && $container.attr('class') == 'team-memeber-box') {
        $container.slideUp("fast");
        $container.remove();
      } else {        
        makeEditMemeberBox($parent, this);
      }
    });
    $('#sumbit-edit').live('click',function()
    {
      if ($('#team_name').val().match(/^(\w)+[\w\d\.]*/)) {

        $form = $(this).parent().parent();
        $.post(base_url + "teams/addTeam", $form.serialize(), function(data){
          $tr = $form.parent().parent().parent();
          var tds = $tr.prev().children();
          $(tds[0]).html(data);
          $tr.remove();
        });
      }
      else
      {
        $('#team-error').text('Team name is not valid.');
        $('#team-error').show();
      }
    });

    $('#sumbit-member').live('click',function()
    {
      $form = $(this).parent().parent();      
      $.post(base_url + "teammembers/addTeammember/", $form.serialize(), function(data){
        $tr = $form.parent().parent().parent();
        var tds = $tr.prev().children();
        $(tds[1]).html(data);
        $tr.remove();
      });
    });
  });
    
 
  function manageTeams(id , element)
  {
    $('#team_id').val(id);
    manage_row = element;
    make_row_selected(element);
    $.ajax({
      type: "GET",
      url: base_url+"teammembers/index/"+id,
      dataType: "json",
      success: function(data){
        data = eval(data);
        view = data.view;
        data = data.data;
        var html = "<div class='header_rgt'><h1>Manage Members</h1></div>\
          <table cellpadding='0' cellspacing='0' width='100%' class='stores-list' border='1px'>\
            <tr><th><strong>Member Name</strong></th><th><strong>Role</strong></th><th><strong>Designation</strong></th><th><strong>Badges</strong></th><th><strong>Actions</strong></th></tr>";            
                    if(data && data.length) {
                      for(var i=0; i < data.length; i++)
                      {
                        html += "<tr>";            
                        html += "<td align = 'center'>" + data[i].name  + "</td>" ;            
                        html += "<td align = 'center'>" +(data[i].role == 'creator'?'Owner': data[i].role) +"</td>" ;
                        html += "<td align = 'center'>" +(data[i].designation == 'expert'?'expert': data[i].designation) +"</td>" ;
                        html += "<td align ='center'>"
                        if(data[i].image_url!= null && data[i].image_url.indexOf('default')!=-1)
                        {              
                          html+=(data[i].image_url != ""?"<img src ='"+base_url+"images/badges/"+data[i].image_url+"'/>":"-");
                        }            
                        else
                        {              
                          html += (data[i].image_url != ""?"<img src ='"+base_url+"uploads/teams/"+data[i].qa_team_id+"/t-"+data[i].image_url+"'  alt ='Image Not Found!!'/>":"-");
                        }
                        html+="</td>" ;
                        html+="<td align = 'center'>";
                        if((view.role == 'admin' && data[i].role != 'creator') || (view.role == 'creator'))
                          html +="<a href ='javascript:;' rel='"+data[i].qa_team_id+":|:"+data[i].qa_team_member_id+"' class ='edit-memeber'>Edit</a>";
                        else
                          html+="-/-";
                        if(data[i].role != 'creator' && view.role != 'view')
                          html+="&nbsp;|&nbsp;<a href ='"+base_url+"teammembers/delete/"+data[i].qa_team_id+"/"+data[i].qa_team_member_id +"'>Delete</a>";
                        html+="</td>";
                        html += "</tr>";
                      }
                    } else {
                      html += "<tr><td align='center' colspan='3'>No team has created yet!</td></tr>" ;
                    }
                    html += "</table>";
                    $('#viewTeamMember').html(html);
                    $('#add_member').slideDown();
                  }
                });
              }
              function makeEditTeamBox(parent, self)
              {
    
                $.get(base_url + "teams/getStoreList/" + $(self).attr('rel'), function(data){
                  data = eval(data);
                  data = data[0];
                  var html ='';
                  html += '<div class="store-container" style="display:none" >';
      
                  html += '<form action="" class="constrain" id = "editForm">';
                  html += '<div><label>Team Name</label> <input type="text" id="team_name" name="teamName" value ="' + data.team_info.team_name + '"/><span class = "error" style = "display:none" id ="team-error"></span></div>' ;          
                  for(var i = 0; i < data.stores.length; i++){
                    //html += '<option value="'+ data.stores[i].qa_store_id+'">'+ data.stores[i].qa_store_name +'</option>';
                  }
                  //html+= '</select></div>' ;
                  html +='<input type="hidden" name = "qa_team_id" value="'+ data.team_info.qa_team_id +'"/>';
                  html+= '<div><input type="button" value="Edit Team" id="sumbit-edit" /></div>' ;
                  html += "</form>";
                  html += "</div>";

                  $container = $('<tr class="team-box"></tr>');
                  $container.html('<td colspan="5" width="100%">'+html+'</td>');
                  $(parent).after($container);
                  $container.find('.store-container').slideDown("fast");
                });
              }

              //  Edit Members
              function makeEditMemeberBox(parent, self)
              {
                var rel = $(self).attr('rel');
                rel = rel.split(':|:');
                var rel = $(self).attr('rel');
                rel = rel.split(':|:');    
                html = '<iframe src="'+base_url + 'teammembers/edit/'+ rel[0]+'/'+rel[1]+'" id = "frame_'+rel[0]+'" width="100%" height="325" scrolling="no" frameborder="0">\
            <p>Your browser does not support iframes.</p>\
            </iframe>';
                $container = $('<tr class="team-memeber-box"></tr>');
                $container.html("<td colspan='5'>"+html+"</td>");
                $(parent).after($container);
                $container.find('.store-container').slideDown("fast");
        
    
              }
              function make_row_selected(element)
              {
                $container = $(element).parent().parent();
                $container.parent().find('tr').removeClass('selected');
                $container.addClass('selected');
              }
              // remove iframe
              function remove_frame(element_id)
              {    
                $('#'+element_id).parent().parent().remove();
                manageTeams($('#team_id').val(),manage_row);
              }
</script>

<style type="text/css">
  #editMember.constrain label{min-width: 150px}
</style>

<div class="rgt_850">
  <br/>
  <div class="heading_section clearfix">
    <div class="head setting">Team Management</div>
    <div class="clear"></div>
  </div>


  <?php
  echo list_records_table($teams, array(array(
          'heading' => 'Team name',
          'text' => 'team_name'
      ), array(
          'text' => 'qa_store_name',
          'heading' => 'Store name'
      ), array(array(
              'text' => 'Edit',
              'heading' => 'Manage Members',
              'rel' => '{qa_team_id}',
              'class' => 'edit-team',
              'compare' => '{role}',
              'compare_with' => 'view'
          ), array(
              'text' => ' | '
          ), array(
              'text' => 'View Members',
              'heading' => 'View Members',
              'callback' => 'manageTeams({qa_team_id},this)'
          )
      )
          ), "rpt_area");
  ?>

</div>
<div id="viewTeamMember">
</div>
<div class="header_rgt" style ="display: none" id ="add_member">
  <div id="confirmation" class="success" style="display:none">The invitation is send successfully</div>
  <div id="error" class="error" style="display:none">The invitation is already sent to this email</div>
  <h4>Search Team Members</h4>

  <form class="constrain" method="" action="" id="addFrm">
    <div class="content_accordian">
      <div id="content_1" class="disp_content_white">

        <div class="row_dat">
        </div>
        <div class="row_dat">
          <div style="width:190px" class="lbel">Enter Email:</div>
          <div class="lbl_inpuCnt" style="width:auto">            
            <input type="text" class="account_med required"  id="user_email" name="user_email" value="Type Your text Here" onclick="this.value=''" onblur="(this.value == ''? this.value ='Type Your text Here':'')"/> <span style="display: none" id="no_record">No Record Found</span><label class="error" id="error_email" style="display: none" >Invalid Email address</label>
            <div id="suggestion" style="display: none"></div>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row_dat">
          <div style="width:190px" class="lbel wd165">&nbsp;</div>            
          <input type="button" id="addBtn" class=" btn_save" tabindex="8" value="" name="edit_seller_info">
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </form>
  <div class="clear"></div>
</div>



