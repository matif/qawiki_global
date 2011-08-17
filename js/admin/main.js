
function ucfirst (str) {
  str += '';
  var f = str.charAt(0).toUpperCase();
  return f + str.substr(1);
}

function remove_item(arr, removeItem) {
  return jQuery.grep(arr, function(value) {
    return value != removeItem;
  });
}

function validate_form (form)
{
  var valid = true;
  var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
  $.each($(form).find('.required'), function(index, element){
    var is_empty = ($(element).val().replace(/\s+/, '') == '');
    var error_msg = 'This field is required';
    var invalid_email = false;
    if(!is_empty && $(element).hasClass('email') && !regExp.test($(element).val())) {
      error_msg = 'Email is not valid';
      invalid_email = true;
    }
    if(is_empty || invalid_email) {
      if(!$(element).next().hasClass('error')) {
        $(element).after('<span class="error">'+error_msg+'</span>');
      } else {
        $(element).next().text(error_msg);
      }
      valid = false;
    } else if($(element).next().hasClass('error')) {
      $(element).next().remove();
    }
  });

  return valid;
}

function clear_form(form)
{
  $(form).find(':input').each(function() {
    switch(this.type) {
      case 'select-one':
      case 'text':
      case 'password':
      case 'textarea':
        $(this).val('');
        break;
      case 'checkbox':
      case 'radio':
        break;
    }
  });

  $(form).find('.error').remove();
}