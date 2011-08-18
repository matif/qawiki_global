<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<link rel="stylesheet" href="<?php echo base_url() . 'css/jquery/jquery-ui.custom.css' ?>" type="text/css" />
<style type="text/css">
  #members, #inactive_memeber{
    min-height: 250px;
    min-width : 400px;
    width: 450px;
    height: 300px;
    border: #e4e4e4 1px solid;
    float: left;
  }

  #container{
    padding-top: 10px;
  }

  #button{
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 100px;
    float:  left;
  }  
  }
</style>
<script type="text/javascript">
  function move_elements(from_element, to_element, add)
  {     
    rel = $(from_element +" li").attr('rel');
    rel = rel.split("|");
    var post_field = rel[0];
    to_element = $(to_element).find('ul', 0);
    $.each($(from_element).find('.selected'), function(index, element){
      $(element).removeClass('selected');
      $clone = $(element).clone();      
      rel = $clone.attr('rel');
      rel = rel.split("|");      
      if(add){        
        $clone.find('input').remove();
        $clone.append('<input type="hidden" name="'+post_field+'[]" value="'+rel[1]+'" />');
      } else {
        $clone.find('input').remove();
      }
      $(to_element).append($clone);
      $(element).remove();
    });
  }
  $(document).ready(function(){
  
    $("#inactive_list").change(function(){   
      var val = $(this).val();
      var items = {};
      var arr = [];
      $.each($('.inact_lst li a'), function(i, element){
        items[$(element).text().toLowerCase()] = $(element).parent().clone();
        arr.push($(element).text().toLowerCase());
      });

      arr.sort();
      if(val == 'desc') {
        arr.reverse();
      }

      var $ul = $('.inact_lst');
      $ul.html('');
      $.each(arr, function(i, text){
        $ul.append($(items[text]));
      });
    });
    
    $("#active_list").change(function(){
      var val = $(this).val();
      var items = {};
      var arr = [];
      $.each($('.act_lst li a'), function(i, element){
        items[$(element).text().toLowerCase()] = $(element).parent().clone();
        arr.push($(element).text().toLowerCase());
      });

      arr.sort();
      if(val == 'desc') {
        arr.reverse();
      }

      var $ul = $('.act_lst');
      $ul.html('');
      $.each(arr, function(i, text){
        $ul.append($(items[text]));
      });
    });    
    
    $('.list_cnt li').live('click', function(){
      rel = $(this).attr("rel");
      rel = rel.split("|");
      if($(this).hasClass('selected'))
      {
        $(this).removeClass('selected');      
      }
      else if(!$(this).hasClass('inactive'))
      {      
        $(this).addClass('selected');
        $('#active_members').val($(this).attr("rel"));

      }
    });
    $("#remove").click(function(){
      move_elements('#active', '#inactive',true);
    });
  
    $("#add").click(function(){
      //    var arr = new Array();
      //    $.each($("#inactive").find('.selected'), function(index, element){
      //       arr [index] = $(element).attr("rel");
      //    });
      //    data = {
      //      "user_id" :arr
      //    }
      //    $.post("<?php echo base_url() ?>/teammembers/checkMember/<?php echo $this->store_id ?>",data,function(data){
      //      data = eval(data);
      //      
      //      if(data && data.length>0)
      //      {
      //        for(i = 0; i < data.length;i++)
      //        {
      //          html += data[i]+",";
      //        }
      //        $("#err_mem").html(html);
      //      }
      move_elements('#inactive', '#active',true);
      //    })
    
    });
    
    $("#save").click(function(){         
      $.post("<?php echo base_url() ?>teammembers/updateStatus/<?php echo $this->store_id ?>", $("#member_data").serialize(),function(){
//        
//        $("#active_list").val("asc");
//        $("#inactive_list").val("asc");
        window.location.reload();
        
      });
    });
  });
  
  
</script>
<?php echo use_javascript('invite'); ?>

<h1 class="black head_title setting"><span class="fl">Web Ring</span>
  <div class="hlp_ares"><a href="javascript:void(0);" class="vtip" title="Web Ring">
      <img width="16" height="16" src="http://repricing.iserver.purelogics.info/Repricing/images/ico_help.png"></a>

  </div>
</h1>
<div class="clear"></div>
<p class="black">Make your Q&amp;A more valuable. Expand your web ring and increase user interaction. For more information about the web ring feature, see our Q&amp;A.</p>
<div class="clear"></div>
<div class="header_rgt" style ="padding-bottom: 10px;" id ="add_member" >
  <div id="confirmation" class="success" style="display:none">The invitation is send successfully</div>
  <div id="error" class="error" style="display:none">The invitation is already sent to this email</div>

  <form class="constrain" method="" action="" id="addFrm">
    <div class="row_dat mt10">
      <input type="hidden" value="1" id="count_rows" name="count_rows">
      <div class="fs18 fl"><strong>Invite &nbsp;</strong></div>
      <div style="width: 500px;" class="lbl_inpuCnt">
        <input type="text" value="" class="account_med fl" style="margin:3px 10px 0 0; width: 207px; height: 16" id="user_email" name="user_email" value="Type Your text Here" onclick="this.value=''" onblur="(this.value == ''? this.value ='Type Your text Here':'')" >
        &nbsp;<a class="button clearfix fl" href="#">
          <span class="lft_area"></span>
          <span class="rpt_content" id="addBtn">Send</span> 
          <span class="rgt_area"></span>														
        </a><span style="display: none" id="no_record">No Record Found</span><label class="error" id="error_email" style="display: none" >Invalid Email address</label></div>
      <div class="clear"></div>
    </div>

    <div>
      <input type="hidden" value="<?php echo $this->team_id ?>" id="team_id" name="teamId" />
    </div>
  </form>
  <div class="clear"></div>
</div>

<div class="clear"></div>
<form action = "" method="post" id = "member_data">
  <div class="error" id ="err_mem"></div>
  <div class="content_dashboard">
    <div class="heading_section  clearfix">
      <div class="head nopad" style="width:auto">Web Ring Members</div>
      <div class="hlp_ares"><a href="javascript:void(0);" class="vtip"><img height="16" width="16" src="images/ico_help.png"></a></div>
      
    </div>
    <div class="content_accordian">
      <div class="noborder  pad10 fs14">
        <div class="seller_list">
          <div class="seller_list_head">Inactive Websites List</div>
          <select class="mt12 fr wid_160px" id="inactive_list">
            <option value="asc" selected="true">Ascending</option>
            <option value="desc">Descending</option>                       
          </select>
          <div class="clear"></div>
          <div class="list_cnt" id="inactive">
            <ul class="inact_lst">
              <!--              <li style="background: slategray" class="inactive" >
                                <a href="javascript:;"class="">Member Name <span style="padding-left: 300px"> Status </span>                  </a> 
                                
                            </li>-->
              <?php foreach ($inactives as $member): ?>
              <?php if(isset($member["status"])):?>
                <li style="background: #e4e4e4" class="inactive" rel="inactive|<?php echo $member["qa_user_id"] ?>">
                    <a href="javascript:;" style ="display:inline-block;"><?php echo isset($member["name"]) ? $member["name"] : $member["email"] ?></a><span style="float: right"> Invited </span> 
                </li>                
              <?php else:?>
                <li rel="inactive|<?php echo $member["qa_user_id"] ?>">
                  <a href="javascript:;"class=""><?php echo isset($member["name"]) ? $member["name"] : $member["email"] ?></a>                
                </li>                
             <?php endif;?>   
              <?php endforeach; ?>            
            </ul>
          </div>
        </div>      
        <div class="btn_are">
          <a class="button clearfix" href="javascript:;">
            <span class="lft_area"></span>
            <span class="rpt_content btn_fs14" id ="add">Move &gt;</span>
            <span class="rgt_area"></span>
          </a>
          <a class="button clearfix mt10" href="javascript:;">
            <span class="lft_area"></span>
            <span class="rpt_content btn_fs14" id="remove" >&lt; Move</span>
            <span class="rgt_area"></span>
          </a>
        </div>
        <div class="seller_list">
          <div class="seller_list_head">Active Websites List</div>
          <select class="mt12 fr wid_160px" id="active_list">            
            <option value="asc" selected="true">Ascending</option>
            <option value="desc">Descending</option>                     
          </select>
          <div class="clear"></div>
          <div class="list_cnt" id="active">
            <ul class="act_lst">            
              <?php foreach ($members as $member): ?>
                <li rel="active|<?php echo $member["qa_user_id"] ?>"><a href="javascript:;" class="user_select" ><?php echo $member["name"] ?></a></li>
              <?php endforeach; ?>

            </ul>
          </div>        
        </div>
        <div class="clear"></div>
      </div>
    </div>
    <div><input type="button" value="" id="save" class="btn_save fr mt10"></div>
</form>
</div>