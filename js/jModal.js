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