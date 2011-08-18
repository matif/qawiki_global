$(document).ready(function(){
  $('.move-items li').live('click', function(){
    if($(this).hasClass('selected'))
      $(this).removeClass('selected');
    else
      $(this).addClass('selected');
  });

  $('.move-actions .button').bind('click', function(){
    var add = false;
    if($(this).attr('rel') == 'add'){
      var $move_to = $(this).parent().next();
      var $move_from = $(this).parent().prev();
      add = true;
    } else {
      var $move_to = $(this).parent().prev();
      var $move_from = $(this).parent().next();
    }
    move_elements($move_from, $move_to, add);
  });

  $('#move-from-btn').bind('click', function(){
    move_elements('move-to', 'move-from');
  });

  $('#generate-report').bind('click', function(){
    validate_custom_report();
  });

  $('.item-picker-btn').bind('click', function(){
    $picker_container = $(this);
    loadJModalDialog($(this).attr('rel'), {width: 400, height: 500}, 'dialogData', disable_add_for_selected);
  });

  $('#item-sort-alpha a').live('click', function(){
    $.get(base_url + 'ajax/'+$(this).attr('rel'), function(response){
      $('#dialogData').html(response);
      disable_add_for_selected();
    });
  });

  $('.item-row .add').live('click', function(){
    $(this).text('ADDED').removeClass('add').addClass('added');
    var $item = $(this).prev();
    var $cont = $picker_container.parent();
    $cont.find('li[rel='+$item.attr('rel')+']').remove();
    $cont = $cont.next().next();
    $cont.find('ul', 0).append('<li rel="'+$item.attr('rel')+'">'+$item.text()+'</li>');
  });

  $('.item-expand').live('click', function(){
    var self = this;
    $.get(base_url + 'ajax/categoryPicker/'+store_id+'/none/'+$(this).attr('rel'), function(response){
      if($(self).parent().find('.items-sub').length > 0){
        $(self).parent().find('.items-sub').remove();
      }

      $(self).parent().append(response);
      disable_add_for_selected();
    });
  });

  $("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
  $("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });
});

function move_elements(from_element, to_element, add)
{
  var post_field = $(to_element).attr('rel');
  to_element = $(to_element).find('ul', 0);
  $.each($(from_element).find('.selected'), function(index, element){
    $(element).removeClass('selected');
    $clone = $(element).clone();
    if(add){
      $clone.append('<input type="hidden" name="'+post_field+'[]" value="'+$clone.attr('rel')+'" />');
    } else {
      $clone.find('input').remove();
    }
    $(to_element).append($clone);
    $(element).remove();
  });
}

function validate_custom_report()
{
  var valid = true

  if($('#mv_category li, #mv_brand li').length == 0){
    alert('Please select at least one category, or brand');
    valid = false;
  } else if($('#mv_fields li').length == 0){
    alert('Please select at least one field');
    valid = false;
  }

  if(valid){
    $('#report-form').submit();
  }

  return valid;
}

function disable_add_for_selected()
{
  var $cont = $picker_container.parent().parent().next().next();
  $.each($cont.find('li'), function(index, element){
    var rel = $(element).attr('rel');
    if($('.item-container-dlg .text[rel='+rel+']').length > 0){
      $('.item-container-dlg .text[rel='+rel+']').next().text('ADDED').removeClass('add').addClass('added');
    }
  });
}