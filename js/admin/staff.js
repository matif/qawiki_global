

var edit_id = null;

$(document).ready(function(){
  $('#add-new').bind('click', function(){
    $('#user_id').val(0);
    $('.data-list tr').removeClass('selected');
    $('#add-form .heading').text('Add Staff');
    clear_form('#add-form');
    if(edit_id) {
      edit_id = null;
      $('#add-form').show();
    } else {
      $('#add-form').toggle();
    }
  });

  $('.delete-record').bind('click', function(){
    var c = confirm('Are you sure, you want to delete this staff?');
    if(c) {
      var self = this;
      $.post(base_url + 'admin/deleteStaff', {user_id : $(self).attr('rel')}, function(response){
        $(self).parent().parent().remove();
      });
    }
  });

  $('.edit-record').bind('click', function(){
    var id = $(this).attr('rel');
    $('#user_id').val(id);
    if(edit_id == id)
      $('#add-form').toggle();
    else
      $('#add-form').show();

    edit_id = id;
    clear_form('#add-form');
    $('#user_name').val($(this).parent().prev().prev().text());
    $('#user_email').val($(this).parent().prev().text());

    $('#add-form .heading').text('Edit Staff');

    $('.data-list tr').removeClass('selected');
    if($('#add-form').css('display') != 'none') {
      $(this).parent().parent().addClass('selected');
    }
  });
});

function save_staff()
{
  $('#add-form div:last-child').find('.error').remove();
  if(validate_form('#add-form')) {
    $.post(base_url + 'admin/saveStaff', $('#add-form').serialize(), function(response){
      if(response == 'email') {
        $('#add-form div:last-child').append('<span class="error middle">Email already exists</span>');
        return false;
      }
      $('#add-form').hide();
    });
  }
  
  return false;
}