

var edit_id = null;
var edit_type = null;

$(document).ready(function(){

  $('.email-guide').bind('click', function(){
    $(this).next().toggle();
  });

  $('#add-new').bind('click', function(){
    $('#template_id').val(0);
    $('.data-list tr').removeClass('selected');
    $('#add-form .heading').text('Add Email Template');
    clear_form('#add-form');
    reset_options();
    if(edit_id) {
      edit_id = null;
      $('#add-form').show();
    } else {
      $('#add-form').toggle();
    }
    if($('#add-form').css('display') != 'none')
      $(window).scrollTop($('#add-form').position().top);
  });

  $('.delete-record').bind('click', function(){
    var c = confirm('Are you sure, you want to delete this template?');
    if(c) {
      var self = this;
      var template_id = $(self).attr('rel');
      $.post(base_url + 'admin/deleteTemplate', {template_id : template_id}, function(response){
        template_types.push($(self).parent().prev().text().toLowerCase());
        $(self).parent().parent().remove();
        if(edit_id == template_id) {
          $('#add-form').hide();
        }
        $('#add-new').show();
      });
    }
  });

  $('.edit-record').live('click', function(){
    var id = $(this).attr('rel');
    edit_id = id;
    $("#template_id").val(edit_id);    
    $.post(base_url+"emailTemplates/edit/"+id,function(data)
    {      
      reset_options(data.type);
      $('#add-form').show();
      $(window).scrollTop($('#add-form').position().top);
      $('#tx_content').val(data.content);
      $('#email_type').val(data.type);
    },"json" );
  });

  $('textarea.tinymce').tinymce({
    // Location of TinyMCE script
    script_url : 'http://qawiki.iserver.purelogics.info/js/tiny_mce/tiny_mce.js',
    width: '100%',
    height: '250px',

    // General options
    theme : "advanced",
    plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

    // Theme options
    /*theme_advanced_buttons1 : "bold,italic,underline,bullist,numlist",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_buttons4 : "",*/
    theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",

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
});

function save_template(type_user)
{
  if(!type_user)
    type_user = "";
  if(type_user == "user")
    url = base_url + "emailTemplates/saveTemplate/"+store_id;
  else
    url = base_url + 'admin/saveTemplate';

  if(validate_form('#add-form')) {
    var type = $('#email_type').val();
    $.post(url, $('#add-form').serialize(), function(response){
      $('#add-form').hide();
      
      if(edit_id) {
        var $parent = $('.edit-record[rel='+edit_id+']').parent();
        $parent.prev().text(ucfirst($('#email_type').val()));
        $parent.prev().prev().text($('#tx_content').val());
      } else {
          
        window.location.reload();
      }
      if(edit_type && $.inArray(edit_type, template_types) == -1)
        template_types.push(edit_type);
      template_types = remove_item(template_types, type);
    });
  }

  return false;
}

function reset_options(type)
{
  var templates = (template_types.length > 0) ? template_types.slice(0, template_types.length) : [];
  var options = '';
  if(type) {
    type = type.toLowerCase();
    if($.inArray(type, templates) == -1) {
      templates.push(type);
      edit_type = type;
    }
  }

  for(var i=0; i<templates.length; i++) {
    options += '<option value="'+templates[i]+'" '+(type && type == templates[i] ? 'selected="selected"' : '')+'>'+ucfirst(templates[i])+'</option>';
  }
  $('#email_type').html(options);
}

