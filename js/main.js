var $edit_button = null;
var current_item = null;

$(document).ready(function(e) { 
  $('#show_hide a').click(function() {
    if($(this).html()!="Hide")
      $(this).html("Hide")
    else
      $(this).html("Show")
    $('#footer_show').toggle(400); 
    return false;
  });
  $('.items').bind('click', function(){
    $('#product, #category, #brand, #historySpam, #viewQuestion, #answer').hide();
    $('.items').removeClass('tab-selected');
    $(this).addClass('tab-selected');
    $('#'+$(this).attr('id').replace('_select', '')).show();
  });
  
  $('.accordian_open').live('click', function(){
    $(this).attr('class', 'accordian_close');
    expand_collapse_content(this);
  });
  
  $('.accordian_close').live('click', function(){
    $(this).attr('class', 'accordian_open');
    expand_collapse_content(this);
  });
  
  $('select[name=main_store_id]').bind('change', function(){
    var r_url = store_redirect_url.replace('{STORE_ID}', $(this).val());
    window.top.location = r_url;
  });
  
  if($(".tray_inner").length > 0){
    $(".tray_inner").jCarouselLite({
      btnPrev: ".move_pre",
      btnNext: ".move_next",
      visible: 7,
      circular: false,
      start: (typeof jcarousel_start != 'undefined' ? jcarousel_start : 0)
    });
  }
  
  attach_tinymce();
});


function doAjax(type, url, data, dataType, callback)
{
  $('#quick_message').show();
  
  dataType = typeof(dataType) != 'undefined' ? dataType : "html";

  $.ajax(
  {
    type: type,
    url: url,
    data: data,
    dataType: dataType,
    success: function(response) {
      $('#quick_message').hide();
      
      if(callback)
        callback(response);
    }
  });
}

function validate_form (form)
{
  var valid = true;
  var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
  $.each($(form).find('.required'), function(index, element){
    var is_empty = ($(element).val().replace(/\s+/, '') == '');
    var error_msg = 'You must enter this field.';
    var is_invalid = false;
    if(!is_empty && $(element).hasClass('email') && !regExp.test($(element).val())) {
      error_msg = 'Email is not valid.';
      is_invalid = true;
    } else if($(element).hasClass('file')) {
      if(!is_empty){
        var ext = $(element).val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['jpg', 'pjpg', 'jpeg', 'pjpeg', 'png', 'gif']) == -1) {
          is_invalid = true;
          error_msg = 'File type is not valid.';
        }
      } else
        is_empty = false;
    }
    if(is_empty || is_invalid) {
      if(!$(element).next().hasClass('error')) {
        $(element).after('<span class="error">'+error_msg+'</span>');
      } else {
        $(element).next().text(error_msg);
      }
      valid = false;
    }
  });

  return valid;
}

function expand_collapse_content(element)
{
  var $parent = $(element).parent();
  if($parent.next().hasClass('content_accordian'))
    $parent.next().toggle();
  else if($parent.parent().hasClass('content_accordian'))
    $parent.parent().toggle();
}

function attach_autocomplete(path, postfix, absolute, element, extraparams, type, callback_func)
{
  if(!type)
    type = 'post';

  $( "#"+(element ? element : 'searchbox') ).autocomplete({
    source: function( request, response ) {
      if(extraparams){
        postfix = $('#'+extraparams).val();
      }
      $.ajax({
        url: base_url+(!absolute ? "main/getStores/" : absolute)+(postfix ? '/'+postfix : ''),
        dataType: "json",
        type: type,
        data: {
          'term': request.term
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
          if(typeof auto_complete_pre_suggest != 'undefined'){
            auto_complete_pre_suggest();
          }
          response($.map( items, function( item ) {
            var rw = {
              label: item.Value,
              value: item.Value,
              Id: item.Id
            };
            if(typeof item.url != 'undefined')
              rw.url = item.url;
            return rw;
          }));
        }
      });
    },
    minLength: 1,
    select: function( event, ui ) {
      if(typeof callback_func != 'undefined') {
        var ret = eval(callback_func)(ui.item);
      } else if(typeof autocomplete_callback != 'undefined') {
        var ret = eval(autocomplete_callback)(ui.item);
      } else {
        window.location.href = base_url+path+'/'+ui.item.Id;
        window.location.reload(ui.item.Id);
      }
    }
  });
}

function is_empty(element)
{
  return ($(element).val().replace(/\s+/, '') == '');
}

function reload_grid_url(element, url)
{
  $(element).setGridParam({
    url: url
  }).trigger('reloadGrid');
}

function ucfirst(text)
{
  return text.substr(0,1).toUpperCase()+text.substr(1);
}

function attach_editable_event()
{
  $('.editable-link').live('click', function(){
    $edit_elem = $(this).parent().parent().find('.editable-text', 0);
    $('#edit_text').val($edit_elem.html());
    $('#edit_save').attr('rel', $(this).attr('rel'));
    showInstantJModal(null, {
      width: 400, 
      height: 180, 
      title: 'Edit'
    }, 'inline-edit-dlg');
  });
}

function attach_button_editable_event()
{  
  $('.editable-button').live('click', function(){
    $edit_elem = $(this).parent().parent().find('span', 0);
    
    $edit_button = $edit_elem.parent();
    
    $('#edit_button').val($edit_elem.text());
    var rel = $(this).attr("rel");
    rel = rel.split('|');
    
    $('#button_save').attr('rel', rel[0]);
    
    var html = '';
    
    if(typeof rel[2] != 'undefined' && rel[2] != ''){
      html += '<div class="row_dat">\
        <div class="lbel">Image Preview:</div>\
        <div id="button-styles" class="lbl_inpuCnt">';
      html += "<img width='30px' height='30px' src ='"+store_url+"t-"+rel[2]+"'alt = '' />"; 
    }
    
    html += '</div>\
        <div class="clear"></div>\
      </div>';
    $("#preview_image").html(html);
    
    showInstantJModal(null, {
      width: 450, 
      height: 475, 
      title: 'Edit Button'
    }, 'inline-edit-btn-dlg');
    
    rel[1] = rel[1].replace('qaw-btn-custom', '').replace(/\s+/,'');
    $('#button-styles a').removeClass('selectImage');
    $('#button-styles .'+rel[1]).addClass('selectImage');
    $('#image_upload').val('');
    $("#image_url").val(rel[1]);
  });
}

function attach_select_image(element)
{    
    rel = $(element).attr('rel'); 
    if($(element).attr('class')!='selectImage')
    {
      $parent = $(element).parent().parent(); 
      $parent.find('.selectImage').removeClass('selectImage');
      $(element).addClass('selectImage');               
      $("#image_url").val(rel);           
    }
    else
    {
      $(element).removeClass('selectImage');      
      $("#image_url").val("");
    }
}
function attach_button_editable_save_event()
{
  $('#button_save').bind('click', function(){    
    var rel = $(this).attr('rel');
    var value = $('#edit_button').val();
        
    var url = base_url + 'settings/saveButtonConfig/'+store_id;
    var params = {service: $('#edit_button_service').val(), button_index: rel, button_text: value, button_class: $("#image_url").val(), button_color: $("#font_image").val(),button_height:$("#image_height").val(),button_width:$("#image_width").val(), default_button:$('#button_style_default').val(), custom_button:$("#button_custom_size").val()};
    if($('#button_style_default').attr('checked'))
      params.button_type = $('#button_style_default').val();
    else      
      params.button_type = $('#button_style_custom').val();
//    button_default_size
    if(value.toString().replace(/\s+/, '') != '' && $("#font_image").val() != '' && ($('#button_style_custom').attr('checked') && $("#image_width").val()!="" && $("#image_height").val()!="" || $('#button_style_default').attr('checked')|| $('#button_default_size').attr('checked')) ){
      
          ajaxFileUpload(url, params, "image_upload");
    }
    else{
      $("#btn_err").html("Button text and color or image height and width can not be empty ");
      $("#btn_err").slideDown("fast");
    }
  });
}

function attach_editable_save_event()
{
  $('#edit_save').live('click', function(){
    var rel = $(this).attr('rel');
    var value = $('#edit_text').val();
    if(value.toString().replace(/\s+/, '') != ''){
      $.post($('#save_edit_url').val(), {
        value: value, 
        type: rel
      }, function(){
        $edit_elem.html(value);
        hideJModalDialog('inline-edit-dlg');
      });
    } 
  });
}

/**
 * function attach_pagination_events
 *
 *
 */
function attach_pagination_events()
{
  /** PAGINATION **/
  $('input[name=page_number]').live('blur', function(){
    var total = $('input[name=page_total]').val();
    var val = parseInt($(this).val());
    val = Math.round(val);
    $(this).val(val);
    if(val < 1 || isNaN(val) || val > total){
      $(this).val(1);
    } else {
      pagination_get(this, $(this).val());
    }
  });
  
  $('input[name=page_number]').live("keyup", function(){
    $(this).keypress(function(event){ 
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13')
      {
        var total = $('input[name=page_total]').val();        
        var val = parseInt($(this).val());
        val = Math.round(val);
        $(this).val(val);
        if(val < 1 || isNaN(val) || val > total){
          $(this).val(1);
        } else {
          pagination_get(this, $(this).val());
        }
      } 
    });
  });
    

  $('.paginition_area .pre, .paginition_area .first, .paginition_area .next, .paginition_area .last').live('click', function(e){
    pagination_get(this, $(this).attr('rel'));
  });

  $('select[name=rec_per_page]').live('change', function(e){
    if($(this).val()!="")
      pagination_get(this, 1);
  });
  
  /** END PAGINATION **/
}

/**
 *
 * function pagination_get
 * 
 * @param element
 * @param value
 *
 */
function pagination_get(element, value)
{
  var $parent = $(element).parent().parent();
  $parent.find('input[name=page_number]', 0).val(value);
  cur_page = $parent.find('input[name=page_number]', 0).val();
  total_page = $parent.find('input[name=page_total]', 0).val();
  var params = {
    current_page : value, 
    rec_per_page: $parent.find('select[name=rec_per_page]', 0).val()
  };
  if(typeof search_term != 'undefined')
    params.term = search_term;
  params.start_date = $('#start_date').val();
  params.end_date = $('#end_date').val();
  params.sort_by = $('#sort-options').val();
  params.items_filter = [];
  
  $.each($('.items-filter li.expand'), function(index, element){
    params.items_filter[params.items_filter.length] = $(element).attr('rel');
  });
  
  var pagination_elem = $(element).parent().parent().attr('id');
  var url = $('#'+pagination_elem+'_url').val();
  if($('#item_id').length > 0)
    url += '/'+$('#item_id').val()+'/'+$('#item_type').val();

  doAjax('post', url, params, 'json', function(response){
    $('#'+pagination_elem+'_data').html(response.data);
    $('.'+pagination_elem+'_pagin').html(response.pagination);
  });
}

function attach_tinymce(element, height)
{
  if($("textarea.tinymce").length == 0){
    return false;
  }
  
  $((element ? '#'+element : 'textarea.tinymce')).tinymce({
    // Location of TinyMCE script
    script_url : 'http://qawiki.iserver.purelogics.info/js/tiny_mce/tiny_mce.js',
    width: '100%',
    height: (height ? height : '100px'),

    // General options
    theme : "advanced",
    plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

    // Theme options
    /*theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist",
  theme_advanced_buttons2 : "",
  theme_advanced_buttons3 : "",
  theme_advanced_buttons4 : "",*/
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist",    
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_buttons4 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_resizing : false,

    // Example content CSS (should be your site CSS)
    content_css : "",
    remove_script_host : false,
    relative_urls : false,
    convert_urls: false,
    cleanup : false,
    verify_html : false,
    entity_encoding: "named",
    elements : 'nourlconvert',

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : "lists/template_list.js",
    external_link_list_url : "lists/link_list.js",
    external_image_list_url : "lists/image_list.js",
    media_external_list_url : "lists/media_list.js",

    // Replace values for the template plugin
    template_replace_values : {
      username : "Some User",
      staffid : "991234"
    }
  });
}

/*
 * Related to jquery ui modal dialog
 */

function showJModalDialog(element, options) {
  if(!options)
    var options = {};
  options.resizable = false;
  options.modal = true;
  options.closeOnEscape = false;
	
  if(typeof options.width == 'undefined')
    options.width = 600;
  if(typeof options.height == 'undefined')
    options.height = 400;
	
  $(element).dialog(options);
	
  if(!$(element).dialog('isOpen'))
    $(element).dialog('open');
}

function hideJModalDialog(element) {
  if(!element)
    element = 'dialogData';
  $('#'+element).dialog('close');
}

function loadJModalDialog(path, options, element, callback)
{
  doAjax('get', path, null, 'html', function(data) {
    data = data.replace(/<script.*>.*<\/script>/ig,""); // Remove script tags			
    data = data.replace(/<\/?link.*>/ig,""); //Remove link tags			
    data = data.replace(/<\/?html.*>/ig,""); //Remove html tag			
    data = data.replace(/<\/?body.*>/ig,""); //Remove body tag			
    data = data.replace(/<\/?head.*>/ig,""); //Remove head tag			
    data = data.replace(/<\/?!doctype.*>/ig,""); //Remove doctype			
    data = data.replace(/<title.*>.*<\/title>/ig,""); // Remove title tags			
    //data = data.replace(/<iframe(.+)src=(\"|\')(.+)(\"|\')>/ig, '<iframe$1src="'+'/'+section+'/'+'$3">');; // Change iframe src			
    //data = data.replace(/<img([^<>]+)src=(\"|\')([^\"\']+)(\"|\')([^<>]+)?>/ig, '<img$1src="'+'/'+section+'/'+'$3" $5/>');; // Change images src			
    data = $.trim(data);
		
    showInstantJModal(data, options, element);
    
    if(typeof callback == 'function')
      callback();
  });	
}

function showInstantJModal(data, options, element) {
  if(!element)
    element = 'dialogData';
	
  if($('#'+element).length == 0)
    $(document.body).append('<div id="'+element+'" style="display:none"></div>');

  if(data)
    $('#'+element).empty().html(data);
  
  showJModalDialog('#'+element, options);
}

function parseElementsForJModal()
{
  $("a[target='link-modal-frame']").each(function() {
    $(this).click(function(e) {
      //window.location.hash = $(this).attr('href').match((/\/([^\/\\]+)\.html/))[1];			
      var path = $(this).attr('rel');
      loadJModalDialog(path);
      e.preventDefault();
    });
  });
}

function ajaxFileUpload(url, params, elementId)
{  
  $.ajaxFileUpload
  (
  {
    url:url, 
    secureuri:false,
    fileElementId: elementId,
    dataType: 'json',
    data: params,
    success: function (data, status)
    {      
      if(data == 1)
      {
        hideJModalDialog('upload-csv-dlg');
        alert("File Uploaded Succesfully");
      }
      if(typeof(data.error) != 'undefined') {
      } else {
        hideJModalDialog('inline-edit-btn-dlg');
        if(data == ''){
          $edit_button.attr('class', 'qaw-button '+params.button_class);
        } else {
          $edit_button.attr('class', 'qaw-button qaw-btn-custom');
          $edit_button.css({'background-image': 'url('+data+')'});          
        }
        $edit_button.find('span', 0).text(params.button_text).css('color', '#'+$('#font_image').val());
      }
    },
    error: function (data, status, e) {
      hideJModalDialog('inline-edit-btn-dlg');
    }
  }
  )
}
/*** UI MODAL JS END ***/
/** ADD LINK, SUGGESTIONS **/
function get_items_by_name()
{
  if($('#browseItem').val() == ''){
    $('#listItemsDialog').html('');
    return false;
  }
  
  doAjax('get', base_url + 'ajax/store_browse/' + store_id + '/' + $('#browseItem').val() + '/' + $('#browseName').val(), null, 'html', function(data){
    $('#listItemsDialog').html(data);
  });
}

function get_sub_categories(me, category_id)
{
  var $parent = $(me).parent().parent();
  
  doAjax('get', base_url + 'ajax/sub_categories/' + store_id + '/' + category_id, null, 'html', function(data){
    if($('#listItemsDialog #sub_cat_'+category_id).length == 0){
      $parent.after(data);
    }
  });
}

function add_link(item_id, item_title, item_url)
{
  var element = 'answer-text';
  if($('#add_link_to').length > 0)
    element = $('#add_link_to').val();

  var content = ' ['+item_title+(item_url != '' ? '|'+item_url : '')+']';
  tinyMCE.execInstanceCommand(tinyMCE.activeEditor.id, 'mceInsertContent', false, content);
}

function add_link_from_suggestion()
{
  if(current_item && $('#sub_cat_dlg').val().replace(/\s+/, '') != ''){    
    add_link(1, current_item.value, current_item.url);    
  }
}

