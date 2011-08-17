
$(document).ready(function(){
  attach_editable_event();  
  attach_editable_save_event();
  attach_button_editable_event();
  attach_button_editable_save_event()
  
  $('#font_color, #font_image').ColorPicker({
    onSubmit: function(hsb, hex, rgb, el) {
      $(el).val(hex);
      $(el).ColorPickerHide();
      var elem_class = color_mapping[$(el).attr('id')];
      $('.'+elem_class).css('color', '#'+hex);
    },
    onBeforeShow: function () {
      $(this).ColorPickerSetColor(this.value);
    },
    onShow: function (colpkr) {
      $(colpkr).fadeIn(300);
      return false;
    },
    onHide: function (colpkr) {
      $(colpkr).fadeOut(300);
      return false;
    },
    onChange: function (hsb, hex, rgb) {
      $(this).val(hex);
    }
  })
  .bind('keyup', function(){
    $(this).ColorPickerSetColor(this.value);
  });
})
