
var search_term = '';

$(document).ready(function(){
  
  attach_pagination_events();
  $.post(base_url+"catalog/get_ftb_file_name/"+store_id,function(data){
    $("#ftp_name").val(data);
  });
  
  $('.edit-item').live('click',function(){
    edit_catalog_item(this);
  });

  $('.question-it').live('click', function(){
    loadJModalDialog(base_url + 'catalog/questionDialog/'+store_id+'/'+$(this).attr('rel'), {width: 902, height:820, dialogClass: 'question-dlg'}, 'questionDlg');
  });
  
  $("#check").click(function(){      
    file_name = $("#ftp_name").val();   
    if(file_name != "")
    {
      upload_csv_file(file_name) 
    }
    else
    {
      showInstantJModal(null, {
        width: 420, 
        height: 155, 
        title: 'Upload CSV'
      }, 'upload-csv-dlg');      
    }    
  });
  
  $("#sumbit_csv").live("click", function(){       
    var fieldvalue = $('#upload_csv').val();
    var thisext = fieldvalue.substr(fieldvalue.lastIndexOf('.'));
    
    if($("#upload_csv").val()!= "" && thisext == ".csv")
    {
      url = base_url + 'catalog/process_ftp_file/'+store_id;    
      data = ajaxFileUpload(url, null, "upload_csv");
      hideJModalDialog('upload-csv-dlg');
      alert("File Uploaded Succesfully");
    }
    else
    {
      alert("Please Upload a valid file");
    }
  });
});

function get_products(item_id, item_type)
{
  doAjax('GET', base_url + 'catalog/products/'+store_id+'/'+item_id+'/'+item_type, null, 'json', function(response){
    $('#productPag_data').html(response.data);
    $('.productPag_pagin').html(response.pagination);
    $('#productsPanel').show();
  });
}

function edit_catalog_item(element)
{
  var rel = $(element).attr("rel");
  var tok = rel.split("/");
  var $parent = $(element).parent().parent();
  var $container = $parent.next();
  if($container && $container.attr('id') == 'item_edit_'+tok[0]) {
    $container.slideUp("fast");
    $container.remove();
  } else {
    doAjax('GET', base_url + 'catalog/editItem/'+store_id+'/'+rel, null, 'html', function(response){
      $container = $('<tr id="item_edit_'+tok[0]+'" ><td colspan="8"></td></tr>');
      $container.find('td').html(response);
      $parent.after($container);
      //$container.slideDown("fast");
    });
  }
}

function save_edit_item(element)
{
  var $parent = $(element).parent().parent();
  
  if(validate_form($parent)){
    var params = $parent.serialize();

    doAjax('post', base_url+'catalog/saveEditItem/'+store_id, params, 'json', function(data){
      var $container = $parent.parent().parent().parent();
      var $me = $container.prev();
      $container.slideUp("fast");
      $container.remove();
      var tds = $me.find('td');
      $(tds[1]).html(data.id);
      $(tds[2]).html(data.title);
      if(typeof data.description != 'undefined')
        $(tds[3]).html(data.description);
    });
  }
  
  return false;
}

function save_question()
{
  var content = tinyMCE.get('question-text').getContent();
  if(content.replace(/\s+/, '') != ''){
    var params = $('#questionForm').serialize();

    doAjax('post', base_url+'catalog/saveQuestion/'+store_id, params, 'html', function(data){
      tinyMCE.get('question-text').setContent('');
      $('#questionSaved').show();
    });
  }
  
  return false;
}

function upload_csv_file(file_name)
{
  doAjax('GET', base_url + 'catalog/process_ftp_file/'+store_id+'/'+file_name,null, '', function(response){          
    if(response == -1)
    {
      alert("File Does not exists");
    }
    else if(response == 1)
    {
      alert("File Uploaded Succesfully");
    }
  });
}

function question_dialog_suggestion(item)
{
  current_item = item;
}

