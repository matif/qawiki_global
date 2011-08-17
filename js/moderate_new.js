
var search_term = '';
//var current_item = null;

$(document).ready(function(){
  
  // attach pagination events
  attach_pagination_events();
  
  //export
  $(".export").click(function(){
    rel = $(this).attr("rel");
    url = base_url+'moderate/export/'+store_id+'/'+rel+"/xls";
    $("#export_xls").attr("href", url);
    url = base_url+'moderate/export/'+store_id+'/'+rel+"/html";    
    export_dlg(url);
  });  
  $("#export_xls, #export_html").click(function(){
    hideJModalDialog('dlg_export');
  });
  
  
  /** START FILTER **/  
  
  $("#start_date, #end_date").datepicker({
    dateFormat: 'yy-mm-dd',
    onSelect: function(dateText, inst) {
      reload_data();
    }
  });
  
  attach_autocomplete(null, '', 'moderate/postSuggest/'+store_id);
  
  $('#searchbox').bind('blur', function(){
    var val = $(this).val();//.replace(/\s+/, '');
    if(val != search_term){
      reload_data(val);
    }
  });
  
  $('.items-filter li').bind('click', function(){
    if($(this).hasClass('expand')){
      $(this).removeClass('expand').addClass('collapse');
    } else {
      $(this).removeClass('collapse').addClass('expand');
    }
    reload_data();
  });
  
  /** END FILTER **/
  
  /* CUSTOM CHECKBOX */
  $('.custom-chk').live('click', function(){
    var $span_tic = $(this).find('span', 0);
    if($span_tic.hasClass('chk-ok')){
      $span_tic.removeClass('chk-ok');
    } else {
      $span_tic.addClass('chk-ok');
    }
  });
  
  $('.view-history').live('click', function(){
    var text = $(this).text();
    var question_id = $(this).attr('rel');
    if(text == 'View History'){
      $(this).text('Hide History');
      doAjax('get', base_url+'moderate/questionHistory/'+store_id+'/'+question_id, null, 'html', function(data){
        $('#history_'+question_id).html(data).show();
      });
    } else {
      $(this).text('View History');
      $('#history_'+question_id).empty().hide();
    }
  });
  
  $('.view-answers').live('click', function(){
    var text = $(this).text();
    var question_id = $(this).attr('rel');
    if(text == 'View Answers'){
      $(this).text('Hide Answers');
      doAjax('get', base_url+'moderate/answersList/'+store_id+'/'+question_id, null, 'html', function(data){
        $('#answers_'+question_id).html(data).show();
      });
    } else {
      $(this).text('View Answers');
      $('#answers_'+question_id).empty().hide();
    }
  });
  
  $('.dp .level a').live('click', function(){
    var params = {post_id: $(this).attr('rel'), can_moderate: $(this).text().toLowerCase()};
    var self = this;
    doAjax('post', base_url+'moderate/saveCanModerate/'+store_id, params, 'html', function(){
      $(self).parent().parent().find('a').removeClass('sel');
      $(self).addClass('sel');
    });
  });
  
  $('.answer-it').live('click', function(){
    loadJModalDialog(base_url + 'moderate/answerDialog/'+store_id+'/'+$(this).attr('rel'), {width: 902, height:820, dialogClass: 'answer-dlg'}, 'answerDlg');
  });
  
  $('.email-it').live('click', function(){
    $('#send_email').val('');
    $('#btnSend').attr('rel', $(this).attr('rel'));
    showInstantJModal(null, {width: 600, height:300}, 'emailDlg');
  });
  
  $('.change-mod-status').live('click', function(){
    var tk = $(this).attr('rel').split('|');
    var params = {status: tk[1]};
    params.questions = [tk[0]];
    change_mod_status(params);
  });
  
  $('#sub_cat_dlg').live('blur', function(){
    if(current_item && $(this).val() != current_item.value){
      current_item = null;
    }
  });
});

function autocomplete_callback(item)
{
  reload_data(item.value);
}

function reload_data(value)
{
  if(value){
    if(search_term == value)
      return false;
    
    search_term = value;
  }
  pagination_get($('select[name=rec_per_page]'), 1);
}

function answer_dialog_suggestion(item)
{
  current_item = item;
}

function auto_complete_pre_suggest()
{
  current_item = null;
}

function save_answer()
{
  var content = tinyMCE.get('answer-text').getContent();
  if(content.replace(/\s+/, '') != ''){
    var params = $('#answerForm').serialize();

    doAjax('post', base_url+'moderate/saveAsnwer/'+store_id, params, 'html', function(data){
      tinyMCE.get('answer-text').setContent('');
      $('#answerSaved').show();
      $('#similarPost').html(data);
    });
  }
  
  return false;
}

function load_answer_dialog_for_similar(question_id)
{
  doAjax('get', base_url + 'moderate/answerDialog/'+store_id+'/'+question_id, null, 'html', function(data){
    $('#answerDlg').html(data);
  });
}

function select_all(flag)
{
  if(flag){
    $('.custom-chk span').addClass('chk-ok');
  } else {
    $('.custom-chk span').removeClass('chk-ok');
  }
}

function change_mod_status(params)
{
  doAjax('post', base_url + 'moderate/changeModStatus/'+store_id, params, 'html', function(data){
    update_question_statuses(params);
  });
}

function change_all_mod_status(mod_status)
{
  if($('.custom-chk span.chk-ok').length == 0)
    return false;
  
  var params = {status: mod_status};
  params.questions = [];
  
  $.each($('.custom-chk span.chk-ok'), function(index, element){
    params.questions[params.questions.length] = $(element).attr('rel');
  });

  change_mod_status(params);
}

function update_question_statuses(params)
{
  for(var i=0; i<params.questions.length; i++){
    if(params.status == 'valid'){
      $('.mod-status-app[rel="'+params.questions[i]+'|valid"]').addClass('selected').removeClass('change-mod-status');
      $('.mod-status-app[rel="'+params.questions[i]+'|valid"]').find('a').text('Approved');
      $('.mod-status-rej[rel="'+params.questions[i]+'|invalid"]').addClass('change-mod-status').removeClass('selected-red');
      $('.mod-status-rej[rel="'+params.questions[i]+'|invalid"]').find('a').text('Reject');
      $('#question_'+params.questions[i]).attr('class', 'green');
    } else {
      $('.mod-status-app[rel="'+params.questions[i]+'|valid"]').addClass('change-mod-status').removeClass('selected');
      $('.mod-status-app[rel="'+params.questions[i]+'|valid"]').find('a').text('Approve');
      $('.mod-status-rej[rel="'+params.questions[i]+'|invalid"]').addClass('selected-red').removeClass('change-mod-status');
      $('.mod-status-rej[rel="'+params.questions[i]+'|invalid"]').find('a').text('Rejected');
      $('#question_'+params.questions[i]).attr('class', 'red');
    }
  }
}

function export_all_questions()
{
  if($('.custom-chk span.chk-ok').length == 0)
    return false;
  
  var questions = '';
  
  $.each($('.custom-chk span.chk-ok'), function(index, element){
    questions += (questions == '' ? '' : ':') + $(element).attr('rel');
  });
  
  url = base_url + 'moderate/export/'+store_id+'/'+questions;
  export_dlg(url);
}

function send_email()
{
  $('#emailError').hide();
  $('#emailSent').hide();
  
  if($('#send_email').val().replace(/\s+/, '') == ''){
    $('#emailError').show();
    $('#emailError').find('span').text('Enter email address.');
    return false;
  }
  
  var tokens = $('#send_email').val().split(',');
  for(var i=0; i<tokens.length;i++){
    tokens[i] = tokens[i].replace(/\s+/, '');
    if(tokens[i]){
      var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");		
      if(!regExp.test(tokens[i])) {
        $('#emailError').show();
        $('#emailError').find('span').text('"'+tokens[i]+'" is not valid email.');
        return false;
      }
    }
  }
  
  doAjax('post', base_url+'moderate/sendEmail/'+store_id+'/'+$('#btnSend').attr('rel'), {emails: $('#send_email').val()}, 'html', function(){
    $('#emailSent').show();
  });
}

function export_dlg(url)
{  
  $("#export_html").attr("href", url);
  showInstantJModal(null, {
    width: 400, 
    height: 180, 
    title: 'Export'
  }, 'dlg_export');
}