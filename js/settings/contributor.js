/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){
  $('#color').ColorPicker({
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
  
});

