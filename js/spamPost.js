/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
$(document).ready(function(e)
{
  $('.update').live('click', function()
  {
    var self = this;
    var mod_status = $(this).parent().find('select', 0).val();
    $.post(base_url+'moderator/updateStatus/'+store_id+'/'+$(this).attr('rel')+'/'+mod_status,function(data){
      $(self).parent().parent().remove();
    });
  });

  attach_autocomplete('moderator/spamPosts', 'spam');
    
  $('.history').bind('click',function()
  {
    var rel = $(this).attr("rel");    
    var url = base_url+'moderator/showHistory/'+rel;
   reload_grid_url("#history_list", url);
   $("#historySpam").show();
   
  });
});

function populateQuestions(url)
{
  var html='';
  $.get(url,function(data){
    data = eval(data);
    var pager = data[0].pager;
    data = data[0].results;    
    html+='<div id="category">\
      <table cellpadding="0" cellspacing="0" width="100%" class="stores-list" border="1px">\
        <thead>\
          <tr>\
            <th><strong>Title</strong></th>\
            <th><strong>Description</strong></th>\
            <th><strong>Change Status</strong></th>\
            <th><strong>View History</strong></th>\
          </tr>\
        </thead>';
      for(i=0; i<data.length;i++)
      {
        html+='<tr>';
          html+="<td>"+ data[i].qa_title + "</td>";
          html+="<td>"+ data[i].qa_description+ "</td>";
          html+="<td align='center'>";
            html+="<select name = moderate >";
            html+="<option value = '"+ data[i].mod_status + "'>"+ data[i].mod_status +"</option>";
            if(data.mod_status!='spam'){
              html+="<option value = 'spam' >Spam</option>";
            }
            html+="<option value = 'in_valid' >Invalid</option>";
            html+="<option value = 'irrelevant' >Irrelevant </option>"
            html+="<option value = 'abusive' >Abusive </option>";
          html+="</select>";
            html+="<a href='javascript:;' class = 'update' rel='"+data[i].qa_post_id +":|:"+data[i].id+"'>Update</a>";
          html+="</td>";
          html+="<td align='center'>";
            html+="<a href='javascript:;' class='history' rel='"+data[i].qa_post_id+"'> Spam History</a>";
          html+="</td>";
        html+="</tr>";                
      }
      html+='</table>';
      html+='<div class="paging_gray">';
      html+=pager;
      html+='</div>';
      html+='</div>';
      $('#category').html(html);
  });

}

function make_row_selected(element)
{
  $container = $(element).parent().parent();
  $container.parent().find('tr').removeClass('selected');
  $container.addClass('selected');
}
 