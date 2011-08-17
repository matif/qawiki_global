
$(document).ready(function(e)
{
  attach_autocomplete('moderator/index');

  $('.update').live('click', function()
  {
    var rel = $(this).attr('rel');    
    var self = this;
    data_view = {
     "type":$("#type").val()
    }    
    var mod_status = $(this).parent().find('select', 0).val();
    $.post(base_url+'moderator/updateStatus/'+store_id+'/'+rel+'/'+mod_status+'/moderate', data_view, function(data){
      $(self).parent().parent().remove();
    });
  });
  
  $('.viewAsnwer').live('click', function(){
    var rel = $(this).attr('rel');
    rel = rel.split('|');

    make_row_selected(this);

    $('#answer_list').setGridParam({
      url: base_url+"moderator/displayAnswer/"+store_id+"/"+rel[1]+"/"+rel[0]
    }).trigger('reloadGrid');

    $('#viewAnswer').show();
  });
  
});

function viewQuestion(id, type, element)
{
  make_row_selected(element);

  $('#question_list').setGridParam({
    url: base_url+"moderator/displayQuestion/"+store_id+"/"+id+"/"+type
  }).trigger('reloadGrid');

  $('#viewQuestion').show();
}

function make_row_selected(element)
{
  $container = $(element).parent().parent();
  $container.parent().find('tr').removeClass('selected');
  $container.addClass('selected');
}

var productSel = 0;

function select_product(id){
  if(id && id !== productSel){ 
    $("#products_list").jqGrid("restoreRow", productSel); 
    productSel = id; 
  }
  $("#products_list").jqGrid("editRow", id, true); 
}
