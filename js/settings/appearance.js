
var color_mapping = {'font_color': 'qaw-text', 'link_color': 'qaw-link', 'action_text_color': 'qaw-action'};
var $edit_elem = null;

$(document).ready(function(){

  $('#font_color , #link_color, #action_text_color, #font_image').ColorPicker({
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

  $('.list_on_off a').bind('click', function(){
    var display = $('#'+$(this).attr('rel')+'_li').css("display");
    if((display == "none" && $(this).text() == "Off") || (display != "none" && $(this).text() == "On"))
      return;
    $(this).parent().parent().find('a').removeClass('function-on');
    $(this).addClass('function-on');
    $('#'+$(this).attr('rel')).val($(this).text().toLowerCase());
    $('#'+$(this).attr('rel')+'_li').toggle();
    var nodes = $('.tab_section li')
    if($(this).text() == 'Off'){
      $('.tab_content').html('');
      for(var i=0; i<nodes.length;i++){
        if($(nodes[i]).css('display') != 'none'){
          $(nodes[i]).click();
          break;
        }
      }
    } else {
      var cnt = 0;
      var visible_tab = false;
      for(var i=0; i<nodes.length;i++){
        if($(nodes[i]).css('display') == 'none'){
          cnt++;
        } else {
          visible_tab = nodes[i];
        }
      }
      if(cnt == nodes.length - 1){
        $(visible_tab).click();
      }
    }
  });
  
  $('#font_family').bind('change', function(){
    $('.widget-container, .widget-container h2, .widget-container p, .widget-container a, .widget-container span, .widget-container div').css('font-family', $(this).val());
  });
  
  $('#width').bind('blur', function(){
    var width = $(this).val();

    if(width < 450){
      width = 450;
      $(this).val(450);
      alert('Minimum allowed width is 450');
    }else if(width > 700){
      width = 700;
    }
    
    $('.widget-container').css('width', width+'px');
  });
  
  $('#height').bind('blur', function(){
    var height = $(this).val();
    
    if(height < 200){
      height = 200;
      $(this).val(200);
      alert('Minimum allowed height is 200');
    }
    
    $('.widget-container').css({'height': height+'px', 'overflow-y': 'auto'});
  });
  
  $(".tab").click(function(){
    rel = $(this).attr("rel");
    $(this).parent().parent().find('li').removeClass('selected');
    $(this).addClass("selected");
    
    $.get(base_url+"settings/getTabsHtml/"+store_id+"/"+rel, function(data){
      $('.tab_content').html(data);
    });
      
  });
  attach_editable_event();
  
  attach_editable_save_event();
  attach_button_editable_event();
  attach_button_editable_save_event()
});