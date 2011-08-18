
$(document).ready(function(){
  $('#format-csv').bind('submit', function(){
    var valid = true;
    if(typeof csv_required != 'undefined'){
      var head= [];
      var error_text = 'Following field(s) are missing: ';
      $.each($('#saved_cols_names input[type=hidden]'), function(index, element){
        head[index] = $(element).val();
      });
      for(var i = 0; i < csv_required.length; i++){
        if($.inArray(csv_required[i], head) == -1){
          valid = false;
          error_text += csv_required[i]+', ';
        }
      }
      
      error_text = error_text.substr(0, error_text.length-2);
      
      if(!valid){
        if($('body').find('.error').length == 0){
          $(this).before('<div class="error"></div><br/>');
        }
        
        $('body').find('.error').html(error_text).show();
      } else if($('body').find('.error').length > 0){
        $('body').find('.error').hide();
      }
    }
    
    return valid;
  });
});


function showEditField(id)
{
  var cols_count = $('#cols_count').val();
  var disableIndexes = new Array();
  var counter = 0;
  for(i=0;i<cols_count;i++)
  {
    if($('#hid_'+i).val() != undefined)
    {
      disableIndexes[counter] = $('#hid_'+i).val();	
      counter++;
    }
  }
  var html = csv_field_selector(id);	
  
  
  html += '<br />';	
  
  html += '<a href="javascript:void(0)" onclick="saveColName('+id+');">Save</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="deleteColField('+id+');">Delete</a>';
  var cols_count = $('#cols_count').val();
  for(var i=0;i<cols_count;i++)
  {	
    if(i == id)
    {
      if($('#th_'+i).attr('class') != 'deletedCol' && $('#th_'+i).attr('class') != 'saved')
      {
        $('#th_'+i).html(html);
        $('#th_'+id).addClass('editSelect');
      }
    }else
    {
      if($('#th_'+i).attr('class') != 'saved')
      {
        var returnHtml = '<p>unnamed column<br /><a href="javascript:void(0);" onclick="showEditField('+i+');">Edit</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="deleteColField('+i+');">Delete</a></p>';	
        $('#th_'+i).html(returnHtml);
        $('#th_'+i).removeClass('editSelect').removeClass('deletedCol').removeClass('saved');
        $('#hid_'+i).remove();
      }
    }
  }
  for(i=0;i<disableIndexes.length;i++)
  {
    $('#'+id).find("option[value="+disableIndexes[i]+"]").attr('disabled', true);
  }
}

function showEditFieldSingle(id)
{
  showEditField(id);
  var cols_count = $('#cols_count').val();
  var disableIndexes = new Array();
  var counter = 0;
  for(i=0;i<cols_count;i++)
  {
    if($('#hid_'+i).val() != undefined)
    {
      disableIndexes[counter] = $('#hid_'+i).val();	
      counter++;
    }
  }
  var html = csv_field_selector(id);
  
  if(id > 0)
  {
    var back_id = parseInt(id)-1;
    html += '<br />';
  }else
  {
    html += '<br />';	
  }
  html += '<a href="javascript:void(0)" onclick="saveColName('+id+');">Save</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="deleteColField('+id+');">Delete</a>';
  $('#th_'+id).html(html);
  $('#th_'+id).addClass('editSelect');
  for(i=0;i<disableIndexes.length;i++)
  {
    $('#'+id).find("option[value="+disableIndexes[i]+"]").attr('disabled', true);
  }
}

function deleteColField(del_id)
{
  var data_html = '<string>deleted</strong><br />won\'t be imported<br /><a href="javascript:void(0);" onclick="showEditFieldSingle('+del_id+')">Edit</a>';
  var cols_count = $('#cols_count').val();
  $('#th_'+del_id).html(data_html);
  $('#th_'+del_id).removeClass('editSelect').removeClass('saved').addClass('deletedCol');
  $('#hid_'+del_id).remove();
  if(del_id < cols_count-1)
  {	
    var id = del_id+1;
    //if(type==0)
      //showEditField(id);
  }
}

function saveColName(id)
{
  if($('#'+id+' :selected').attr('disabled')){
    return false;
  }
  
  var txt = $('#'+id+' :selected').text();
  var val = $('#'+id+' :selected').val();
  var cols_count = $('#cols_count').val();
  $('#hid_'+id).remove();
  $('#saved_cols_names').append('<input type="hidden" name="hid_'+id+'" id="hid_'+id+'" value="'+val+'" />');
  var html = txt+"<br />"+val;
  html += '<br /><a href="javascript:void(0)" onclick="showEditFieldSingle('+id+');">Edit</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="deleteColField('+id+');">Delete</a>';
  $("#th_"+id).removeClass('editSelect').removeClass('deletedCol').addClass('saved');
  $("#th_"+id).html(html);
  if(id < cols_count-1)
  {	
    showEditField(id+1);
  }
}

function changeBg(id)
{
  $('#'+id).css('background-color', '#F0F0F0');
}

function csv_field_selector(id)
{
  var html = '<select id="'+id+'" name="'+id+'" onclick="changeBg('+id+');">';
  
  for(key in csv_fields)
  {
    html += '<option value="'+key+'">'+csv_fields[key]+'</option>';
  }
  
  html += '</select>';
  
  return html;
}
