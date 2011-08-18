$(document).ready(function(){
  
  if(typeof controller_action == 'undefined')
    controller_action = 'question';
  
  attach_editable_event();  
  attach_button_editable_event();
  attach_editable_save_event();
  
  populateCategory();
  
 
  $(".function-state").bind("click",function(){
    $(this).parent().parent().find('a').removeClass('function-on');
    
    if($(this).hasClass("function-on") == false)
    {
      $(this).addClass('function-on');

      var self = this;
      var data = {
        type: $(this).attr('rel'),
        value: $(this).text().toLowerCase()
      };            

      $.post(base_url+"settings/"+controller_action+"/"+store_id, data, function(){
        handle_option(data);
      });
    }
  });

  $( "#add_link" ).autocomplete({
    source: function( request, response ) {
      $.ajax({
        url: base_url+"/../ajax/getStorePosts/"+store_id+"/"+$("#options").val()+"/"+request.term,
        dataType: "json",
        data: {
        },
        success: function(items) {
          if(items == -1)
            $("#no_record").show();
          else
            $("#no_record").hide();
          if(!items || items.length == 0) {
            if(typeof auto_complete_no_result != 'undefined')
              auto_complete_no_result();
            return false;
          }
          response($.map( items, function( item ) {              
            return {
              label: item.title,
              value: item.title,
              Id: item.Id
            }
          }));
        }
      });
    },
    minLength: 1,
    select: function( event, ui ) {
      var callback_func = '';
      if(callback_func != '') {
        var ret = eval(callback_func)(ui.item);
      }
    }
  });
  
  $('.editable-link').bind('click', function(){
    $('#edit_item').val($(this).attr('rel'));
    showInstantJModal(null, {width: 400, height: 180, title: 'Edit'}, 'inline-edit-dlg');
  }); 
});

function populateCategory()
{
  $.get(base_url+"ajax/store_browse/"+store_id+"/"+$("#options_name").val()+"/"+$("#options_browse").val(), {}, function(data){        
    var html = "";
    for(i = 0; i< data.length; i++)
    {
      html += '<div class="row_white_lnk clearfix">';        
      html +='<div class="expand_collase"><a href="#">';
      if(typeof(data[i].qa_category_name)!='undefined')
        html += data[i].qa_category_name; 
      else if(typeof(data[i].qa_category_name)!='undefined')
        html += data[i].qa_brand_name;
      else
        html += data[i].qa_product_title;
      html += '</a></div>';
      html += '<div class="ad_lnk"><a href="#">Add Link</a></div>';
      html += '</div>';          
    }
    $("#posts").html(html);
  }, 'json');
}

function handle_option(data)
{
  if(data.value == 'on'){
    $('#options, #options_browse').append('<option value="'+data.type+'">'+ucfirst(data.type)+'</options>');
    $('#auto-items-panel').show();
  } else {
    $('#options, #options_browse').find('option[value='+data.type+']').remove();
    if($('#options').find('option').length == 0) {
      $('#auto-items-panel').hide();
    }
  }
}