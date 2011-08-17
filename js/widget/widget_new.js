
/**
 * qawikiHelper class
 * 
 */
var qawikiHelper = function() {
  this.answers_count = {};
  this.auto_item = null;

  this.set_answer_count = function(qid, cnt, offset) {
    this.answers_count[qid] = cnt;
  }

  this.reset_count = function(){
    this.answers_count = {};
  }

  this.answer_more = function(rel, offset) {
    offset = parseInt(offset) + 5;
    rel = rel.split('/');
    if(typeof this.answers_count[rel[2]] != 'undefined' && parseInt(this.answers_count[rel[2]]) > offset)
      return '<p class="qawiki-see-more"><a href="javascript:;" onclick="qaw_widget.load_answers(this,'+offset+')" rel="'+rel[0]+'/'+rel[1]+'/'+rel[2]+'">see more</a></p>';

    return '';
  }

  this.page_height = function() {
    return window.innerHeight != null ?
      window.innerHeight
      : document.documentElement && document.documentElement.clientHeight ?
          document.documentElement.clientHeight
          : document.body != null ? document.body.clientHeight
        : null;
  }
  
  this.autocomplete_event = function(url){
    var self = this;
    $( "#qaw-search-item" ).autocomplete({
      appendTo: '#qaw-widget',
      source: function( request, response ) {
        $.ajax({
          url: url+'/'+request.term,
          dataType: "json",
          data: {
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
            self.auto_item = null;
            response($.map( items, function( item ) {              
              return {
                label: item.Value,
                value: item.Value,
                Id: item.id,
                url: item.url
              }
            }));
          }
        });
      },
      minLength: 1,
      select: function( event, ui ) {
        var callback_func = '';
        if(callback_func != '') {
          var ret = eval(callback_func)(ui.item);
        }
        self.auto_item = ui.item;
      }
    });
  }
}

/**
 * qawikiWidget class
 *
 */
var qawikiWidget = function(options) {

  /* attributes */
  this.base_url = options.base_url;
  this.store_id = options.store_id;
  this.item_id = options.item_id;
  this.item_type = options.item_type;
  this.owner_id = options.owner_id;
  this.customer_id = options.customer_id;
  this.customer_email = options.customer_email;
  this.data = null;
  this.permission = null;
  this.list_filter = 'all';
  this.default_img = '/../images/widget/qa_img.jpg';
  this.json_param = "?tagmode=any&format=json&jsoncallback=?";
  this.qa_helper = new qawikiHelper();
  this.qawiki_html = new qawikiHtml();
  this.qawiki_util = new qawikiUtil();
  this.qaw_dialogs = new qawDialogs();
  this.popup_link_id = null;

  /* functions */
  this.load = function(){
    var self = this;
    var params = this.qawiki_util.processScriptPath();
    var navigate_to_post = this.qawiki_util.processParams(window.location.href, 'qa_post_id');
    this.base_url = params['base_url'];
    var url = this.base_url + '/main/' + this.owner_id + '/' + this.store_id + '/' + this.item_id + '/' + this.item_type + '/' + this.customer_id + '/' + navigate_to_post + this.json_param;
    url += this.qawiki_util.queryString;
    if(this.customer_email != 'null')
      url += '&e='+this.customer_email;
    else if(params['e'] != 'undefined'){
      this.customer_email = params['e'];
    }
    $.getJSON(url, function(response){
      self.data = eval(response);
      self.render.call(self);
    });

    self.attach_events();

    return this;
  }

  this.attach_events = function(){
    var self = this;
    $('#qaw-widget').live('click', function(e){
      var $target = $(e.target);

      // parent checks
      if($target.hasClass('qawiki-add') || $target.hasClass('qawiki-linked-prod')) {
        $target = $target.parent();
      } else if($target.parent().hasClass('qawiki-cat')){
        $target = $target.parent();
      }

      // expand question
      if($target.hasClass('qaw-expand')) {
        self.load_answers($target, 0);
      } else if($target.hasClass('qaw-collapse')) {
        var qid = $target.attr('rel').split('/');
        $('#qaw-answer'+qid[2]).slideUp(300);
        $target.attr('class', 'qaw-expand');
      
      } else if($target.parent().attr('id') == 'qaw-answer-it') { // add answer
        if(!self.is_logged_in()) {
          return false;
        }
        
        self.show_answer_dialog($target);
        
      } else if($target.parent().attr('id') == 'qaw-ask-question') { // ask question
        if(!self.is_logged_in()) {
          return false;
        }
        //self.toggle_text($target.parent());
        self.popup_link_id = 0;
        //$('#qaQuestForm').toggle();
        self.qawiki_html.qawiki_popup(self.qa_helper, self.qaw_dialogs.questionDialog('question', self.data.question_dlg), {width: 702, dlg_class:'qaw-dlg-no-bg'});

      } else if($target.parent().attr('id') == 'qaQuestAns') { // hide-show list
        self.toggle_text($target.parent());
        $('#qawikiContent').toggle();
        
      } else if($target.parent().parent().parent().hasClass('qawiki-sub-links')) { // sub links
        $('.qawiki-sub-links a').removeClass('qawiki-selected');
        $target.addClass('qawiki-selected');
        self.load_questions.call(self, $target.attr('rel'));

      } else if($target.parent().hasClass('qawiki-save-answer')){ // save answer
        self.save_post($target.parent(), 'Answer');

      } else if($target.parent().attr('id') == 'qaw-save-qa') { // save question
        self.save_post($target.parent());

      } else if($target.parent().hasClass('qaw-spam') || $target.parent().hasClass('qaw-spam-cancel')) { // spam show, cancel
        if(!self.is_logged_in()) {
          return false;
        }
        self.report_spam_form($target.parent());

      } else if($target.parent().hasClass('qaw-spam-save')) { // spam save
        self.save_spam_report($target.parent());
        
      } else if($target.hasClass('qaw-vote-up')) { // thumbs up
        if(!self.is_logged_in()) {
          return false;
        }
        self.save_vote($target, 'up', $target.next());

      } else if($target.hasClass('qaw-vote-down')) { // thumbs down
        if(!self.is_logged_in()) {
          return false;
        }
        self.save_vote($target, 'down', $target.prev());

      } else if($target.hasClass('qawiki-link-products')) { // link products
        self.popup_link_id = $target.attr('id').replace('qawiki-link-prod', '');
        self.search_popup();

      } else if($target.hasClass('qawiki-cat')){ // categories list
        $('.qawiki-cat').removeClass('qawiki-cat-selected');
        $target.addClass('qawiki-cat-selected');
        self.products_list($target);
        
      } else if($target.hasClass('qaw-dlg-close')){ // popup close
        self.qawiki_html.remove_dialog();
        
      } else if($target.parent().hasClass('qawiki-add-product')){ // add product
        self.show_linked_product($target.parent());

      } else if($target.hasClass('qawiki-remove')){ // remove product
        var $parent = $target.parent();
        $parent.parent().find('.qawiki-link-products', 0).show();
        $parent.remove();

      } else if($target.parent().attr('id') == 'qaw-search-btn') {
        //$('.qawiki-sub-links a[rel=all]').click();
        self.load_questions.call(self);
      } else if($target.hasClass('qaw-contributor')) {
        var user_id = $target.attr('rel');
        $.getJSON(self.base_url+'/contributorDetail/'+self.data.session_key+'/'+user_id+self.json_param, function(response){
          self.qawiki_html.qawiki_popup(self.qa_helper, response.html, {width: 702, dlg_class:'qaw-dlg-no-bg'});
        });
      } else if($target.parent().hasClass('qaw-tab')) {
        self.switch_tab.call(self, $target);
      }

      return true;
    })
    
    $('.qawiki-sort-options').live('change', function(){
      self.load_questions.call(self, $(this).val());
    });
  }

  this.render = function() {
    if(this.data) {
      this.data = this.data[0];
      if(this.customer_email) {
        var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
        if(!regExp.test(this.customer_email)) {
          this.customer_email = '';
        }
      }
      if(parseInt(this.data.info['qa_who_can_comment']) != 4 && this.customer_id != '' && this.customer_email == '') {
        $('#qaw-widget').text('Email is not configured');
        return false;
      }
      this.customer_id = this.data.cid;
      this.permission = this.data.info.qa_permission;
      $container = $('<div></div>');
      $container.append('<div class="qaw-glob-header"><div class="qaw-left"></div><div class="qaw-middle"></div><div class="qaw-right"></div></div><div class="qaw-clear"></div>');
      $container.append(this.item_info());
      this.qawiki_html.wrap_main($container);
      
      this.attach_share_options();
    }
  }

  this.item_info = function() {
    $item = $('<div class="qaw-glob-content"></div>');
    $item.append('<div class="qaw-glob-heading">'+this.data.appearance.title.replace('#Product', this.data.info.item_name)+'</div>\
      <div><a href="javascript:;" class="'+(this.data.appearance.functions.ask_question.class.indexOf('custom') > -1 ? 'qaw-custom-btn ' : 'qaw-button ')+this.data.appearance.functions.ask_question.class+'" id="qaw-ask-question"><span>'+this.data.appearance.functions.ask_question.text+'</span></a></div>\
      <div class="qaw-clear"></div>\
      <div class="qaw-tab-section">\
        <ul>\
          <li class="qaw-selected qaw-tab">\
            <a href="javascript:;" rel="popular">'+this.data.appearance.functions.tabs.popular_tab+'</a>\
          </li>\
          <li class="qaw-tab">\
            <a href="javascript:;" rel="recent">'+this.data.appearance.functions.tabs.recent_tab+'</a>\
          </li>\
          <li class="qaw-tab">\
            <a href="javascript:;" rel="unanswered">'+this.data.appearance.functions.tabs.unanswered_tab+'</a>\
          </li>\
        </ul>\
        <div class="qaw-clear"></div>\
      </div>');

    // clear
    //$item.append('<div class="qaw-clear"></div>');
    // icon img and item details
    //$item.append(this.header_section());
    // contents
    $content = $('<div class="qaw-tab-content">\
      <div style="padding: 10px 0">\
        <input type="text" class="qaw-search-text" value="" id="qaw-search-text" />\
        <a href="javascript:;" class="'+(this.data.appearance.functions.search_button.class.indexOf('custom') > -1 ? 'qaw-custom-btn ' : 'qaw-button ')+this.data.appearance.functions.search_button.class+'" id="qaw-search-btn"><span>Search</span></a>\
        <div class="qaw-clear"></div>\
        </div>\
        <div id="qawContent">'+this.questions_list(this.data)+'</div>\
      </div>'
    );
    
    //$content.append(this.qawiki_html.get_sub_nav());
    //$content.append(this.qawiki_html.sort_options());
    //$content.append('<div class="qawiki-clear"></div>');
    //$content.append(this.qawiki_html.search_section());
    //$content.append(this.questions_list(this.data));

    $item.append($content);

    return $item;
  }

  this.header_section = function(){
    var settings = this.data.widget.widget_settings;
    $header = $('<div class="qawiki-content"></div>');
    $header.append('<div class="qawiki-content-left">\
          <img src="'+(settings && typeof settings.icon_path != 'undefined' ? this.base_url +'/../uploads/t-'+ settings.icon_path : this.base_url + this.default_img)+'" />\
        </div>\
        <div class="qawiki-content-right">\
          <h3>'+this.data.info.item_name+'</h3>\
          '+(typeof this.data.info.item_description != 'undefined' ? '<p>' + this.data.info.item_description + '</p>' : '')+'\
          '+($.inArray(this.permission, ['question', 'both']) != -1 ? '<div><div class="qawiki-button" id="qaAskQuest"><a href="javascript:;">Show Ask a Question</a></div>\
          <div class="qawiki-button qa-button-selected" id="qaQuestAns"><a href="javascript:;">Hide Questions and Answers</a></div></div>' : '')+'\
          <div class="qawiki-clear"> </div>\
        </div>\
        <div class="qawiki-clear"></div>\
        '+($.inArray(this.permission, ['question', 'both']) != -1 ? '\
          <div class="qawiki-blue-corners" id="qaQuestForm" style="display:none">\
          '+this.blue_round_section('top') + '\
          <div class="qawiki-bluish-content">\
            <form action="" enctype="multipart/form-data" method="post">\
              <h3>Question</h3>\
              <input type="text" class="qawiki-input-text qawiki-required" name="qawiki_question" />\
              <h3>Additional Information (optional)</h3>\
              <textarea class="qawiki-input-text" name="qawiki_description"></textarea>\
              '+(this.check_image_option('Question') ?
                '<h3>Image (optional)</h3>\
                <input class="qawiki-input-text qawiki-required qawiki-file" name="qawiki_image" type="file" />' : ''
              )+'<h3>Products related to my answer</h3>\
              <div class="qawiki-floating">\
                '+(this.qawiki_html.link_products(0))+'\
              </div>\
              <div class="qawiki-clear"></div>\
              '+(!this.data.nick_name ? '<h3>Nickname</h3>\
              <input class="qawiki-input-text qawiki-required" name="qawiki_nickname" />' +
                (parseInt(this.data.info['qa_who_can_comment']) == 4 ? '<h3>Email</h3>\
                  <input class="qawiki-input-text qawiki-required qawiki-email" name="qawiki_email" />'
                : '')
              : '')+'\
              <div><input type="checkbox" name="qawiki_email_opt" value="1" />Please send me an email when my question is answered.(optional)</div>\
              <div class="qawiki-button-section">\
                <div style="margin: 0pt;float:right" class="qawiki-button qa-button-selected" id="qawikiSaveQuest"><a href="javascript:;">Submit Question</a></div>\
                <div class="qawiki-clear"></div>\
              </div>\
            </form>\
          </div>\
          ' + this.blue_round_section('bottom') : ''
        )+'\
      </div>'
  );

    return $header;
  }
  
  /**
   *
   * function questions_list
   *
   */
  this.questions_list = function(data){
    var questions = '<div class="qaw-question-list"></div>';
    if(data.questions) {
      var rows = data.questions;
      for(var i = 0; i < rows.length; i++) {
        this.qa_helper.set_answer_count(rows[i].qa_post_id, rows[i].total_answers, 0);
        questions += '<div class="qaw-question-row">\n\
            <div class="qaw-user-img"><a href="javascript:;"><img width="71" height="60" title="" alt="" src="http://qawiki.iserver.purelogics.info/images/frontend/user_img.gif"></a></div>\
            <div class="qaw-question-detail">\
              <h2><a class="qaw-link" href="javascript:;">'+rows[i].qa_title+'</a></h2>\
              <p>\
                Asked by <a class="qaw-link qaw-contributor" href="javascript:;" rel="'+rows[i].qa_user_id+'">'+rows[i].name+'</a>\
               '+this.qawiki_html.vote_content(this.data.appearance.functions, this.data, rows[i])
                +this.qawiki_html.spam_report(this.data.info.moderation_type, rows[i].qa_post_id)+'</p>\
              </p>\
              <div>\
                <a href="javascript:;" class="'+(this.data.appearance.functions.answer_it.class.indexOf('custom') > -1 ? 'qaw-custom-btn ' : 'qaw-button ')+this.data.appearance.functions.answer_it.class+'" id="qaw-answer-it" rel="'+rows[i].qa_post_id+'"><span>'+this.data.appearance.functions.answer_it.text+'</span></a>\
                <div class="qaw-share-panel">\
                  <a class="qaw-link qaw-expand" href="javascript:;" rel="'+rows[i].qa_ref_id+'/'+rows[i].qa_post_type+'/'+rows[i].qa_post_id+'">'+rows[i].total_answers+' Answers </a>\
                  <a class="qaw-action qaw-share-options" style="background:none" href="javascript:;">Share  ? </a>\
                </div>\
              </div>\
              <div class="qaw-clear"></div>\
            <div id="qaw-answer'+rows[i].qa_post_id+'" class="qaw-answer-list"></div>\
            </div>\
            <div class="qaw-clear"></div>\
          </div>'
        ;
      }

      questions += '<div class="qaw-pagination">'+data.pagination+'</div>';
    }

    return questions;
  }

  this.answers_list = function(rows, rel, offset) {
    var html = '';
    if(rows.length > 0) {
      for(var i = 0; i < rows.length; i++) {
        html += '<div class="qaw-answer-row">\
          <h2><a class="qaw-link" href="javascript:;">'+rows[i].qa_title+'</a></h2>\
          <!--p>'+(rows[i].image_url ? '<img src="'+this.base_url+'/../uploads/stores/'+this.store_id+'/t-'+rows[i].image_url+'" align="top"/> ' : '')+'<span class="qawiki-details">'+rows[i].qa_description+'</span></p-->\
          <p>\
            Answered by <a class="qaw-link qaw-contributor" href="javascript:;" rel="'+rows[i].qa_user_id+'">'+rows[i].name+'</a>\
           '+this.qawiki_html.vote_content(this.data.appearance.functions, this.data, rows[i])+(this.data.info.vote_type > 1 && this.data.info.moderation_type == 1 ? ' | ' : '')
           +this.qawiki_html.spam_report(this.data.info.moderation_type, rows[i].qa_post_id)+'</p>\
          </p>\
          <!--'+this.designate_badge(rows[i])+'\
          '+/*this.top_contributor(rows[i])+*/'\
          <p>'+this.qawiki_html.vote_content(this.data.appearance.functions, this.data, rows[i])+(this.data.info.vote_type > 1 && this.data.info.moderation_type == 1 ? ' | ' : '')+
          this.qawiki_html.spam_report(this.data.info.moderation_type, rows[i].qa_post_id, 'qawiki-f11')+'</p>\
          '+this.related_products(rows[i], 'answer')+'\
          '+this.qawiki_html.load_video(rows[i])+'-->\
          <div id="qaw-answer'+rows[i].qa_post_id+'" style="display:none"></div>\
        </div>\
        <div class="qawiki-clear"></div>';
      }

      html += this.qa_helper.answer_more(rel, offset);
    } else if(offset == 0) {
      html += '<p>No answer given yet!</p>';
    }

    return html;
  }

  this.answer_form = function(element, qid){
    if($('#answer_form'+qid).length > 0) {
      $('#answer_form'+qid).remove();
      return false;
    }
    this.popup_link_id = qid;

    $parent = $(element).parent();
    $parent.append('<div id="answer_form'+qid+'">\
        <form action="" enctype="multipart/form-data" method="post">\
          <h3>Answer</h3>\
          <textarea class="qawiki-input-text qawiki-required" name="qawiki_answer"></textarea>\
          <h3>Additional Information (optional)</h3>\
          <textarea class="qawiki-input-text" name="qawiki_description"></textarea>\
          '+(this.data.info.video_option == 2 ?
            '<h3>Video URL (optional)</h3>\
              <input class="qawiki-input-text" name="qawiki_video_url" />\
              <p class="qawiki-note">(Paste the URL from your videos on YouTube)</p>\
              <h3>Video Caption (optional)</h3>\
              <input class="qawiki-input-text" name="qawiki_video_caption" />' : ''
          )+(this.check_image_option('Answer') ?
            '<h3>Image (optional)</h3>\
            <input class="qawiki-input-text qawiki-required qawiki-file" name="qawiki_image" type="file" />' : ''
          )+'<h3>Products related to my answer</h3>\
          <div class="qawiki-floating">\
            '+(this.qawiki_html.link_products(qid))+'\
          </div>\
          <div class="qawiki-clear"></div>\
          '+(!this.data.nick_name ? '<h3>Nickname</h3>\
            <input class="qawiki-input-text qawiki-required" name="qawiki_nickname" />' +
            (parseInt(this.data.info['qa_who_can_comment']) == 4 ? '<h3>Email</h3>\
              <input class="qawiki-input-text qawiki-required qawiki-email" name="qawiki_email" />'
            : '')
          : '')+'\
          <div class="qawiki-button-section">\
            <div class="qawiki-save-answer"><a href="javascript:;"></a></div>\
          </div>\
          <input type="hidden" name="qawiki_question_id" value="'+qid+'" />\
          <div class="qawiki-clear"></div>\
        </form>\
      </div>'
    );

    $(window).scrollTop($('#answer_form'+qid).position().top);
  }
  
  this.report_spam_form = function(element){
    var qid = $(element).attr('rel');
    if($('#qaw_spam'+qid).length > 0) {
      $('#qaw_spam'+qid).slideUp(300, function(){
        $('#qaw_spam'+qid).remove();
      });
      return false;
    }

    var $element = $('#qaw-answer'+qid);
    $element.before('<div id="qaw_spam'+qid+'" class="qaw-spam-content" style="display:none">\
        <form action="">\
          <h3>Describe the issue:</h3>\
          <textarea class="qaw-required" name="qaw_issue"></textarea>\
          <div class="qawiki-button-section">\
            <a class="qaw-buton-gray qaw-button qaw-spam-save" href="javascript:;"><span>Submit</span></a>\
            <a class="qaw-buton-gray qaw-button qaw-spam-cancel" href="javascript:;" rel="'+qid+'"><span>Cancel</span></a>\
          </div>\
          <input type="hidden" name="qaw_post_id" value="'+qid+'" />\
          <div class="qaw-clear"></div>\
        </form>\
      </div>'
  );

    $('#qaw_spam'+qid).slideDown(300);
  }

  this.load_questions = function(type, offset, tab_type){
    if(!offset) offset = 0;
    if(!tab_type) tab_type = 'popular';
    
    this.list_filter = type;
    var self = this;
    var key = $('#qaw-search-text').val();
    $.getJSON(this.base_url + '/questions/' + this.data.session_key + '/' + type + '/' + offset + '/' + key + this.json_param, function(response){
      if(key.replace(/\s+/, '') == ''){
        $('#qawiki-search-result').text('').hide();
      } else {
        $('#qawiki-search-result').text('Search results for "'+key+'"').show();
      }
      response = eval(response);
      response = response[0];
      self.qa_helper.reset_count();
      $('#qawContent').html(self.questions_list.call(self, response));
      self.attach_share_options();
    });
  }

  this.paginate = function(offset) {
    this.load_questions (this.list_filter, offset);
  }

  this.load_answers = function(self_expand, offset) {
    var qid = $(self_expand).attr('rel');
    var self = this;
    $.getJSON(this.base_url+'/answers/'+this.data.session_key+'/'+qid+'/'+offset + this.json_param, function(response){
      response = eval(response);
      qid = qid.split('/');
      qid = qid[2];
      $container = $('#qaw-answer'+qid);
      if(offset == 0) {
        $container.hide();
        $container.html(self.answers_list.call(self, response, $(self_expand).attr('rel'), offset));
        $container.slideDown(300);
      } else {
        $container.append(self.answers_list.call(self, response, $(self_expand).attr('rel'), offset));
        $(self_expand).parent().remove();
      }
      
      $(self_expand).attr('class', 'qaw-collapse');
    });
  }

  this.validate_form = function(form) {
    var valid = true;
    var self = this;
    var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
    $.each($(form).find('.qaw-required'), function(index, element){
      var is_empty = ($(element).val().replace(/\s+/, '') == '');
      var error_msg = 'You must enter this field.';
      var is_invalid = false;
      if(!is_empty && $(element).hasClass('qaw-email') && !regExp.test($(element).val())) {
        error_msg = 'Email is not valid.';
        is_invalid = true;
      } else if($(element).hasClass('qaw-file')) {
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
        if(!$(element).next().hasClass('qaw-error')) {
          $(element).after('<div class="qaw-error">'+error_msg+'</div>');
        } else {
          $(element).next().text(error_msg);
        }
        valid = false;
      } else {
        if($(element).attr('name') && $(element).attr('name').indexOf('nickname') > -1)
          self.data.nick_name = $(element).val();
        if($(element).next().hasClass('qaw-error'))
          $(element).next().remove();
      }
    });
    
    return valid;
  }

  this.clear_form = function(form) {
    $(form).find(':input').each(function() {
      switch(this.type) {
        case 'select-one':
        case 'text':
        case 'textarea':
          $(this).val('');
          break;
        case 'checkbox':
        case 'radio':
          break;
      }
    });


  }

  this.save_post = function(element) {
    var $form = $('#qaw-form-qa');
    var self = this;
    if(this.validate_form($form)) {
      $.getJSON(this.base_url+'/savePost/'+this.data.session_key+'/'+this.json_param, $form.serialize(), function(response){
        if(response == 'spam') {
          if(!$(element).prev().hasClass('qaw-error'))
            $(element).before('<span class="qaw-error">Content contains obnoxious words</span>');
          return false;
        }
        $('#qaw-similar-post').html('');
        if(typeof response.similar != 'undefined'){
          $('#qaw-qa-dlg-cont').html(response.similar);
        }
        //$('#qaw-qa-saved').show();
        //self.clear_form('#qaw-form-qa');
        //$parent.html('<div class="qaw-success">Saved successfully.</div>');
      });
    }
  }

  this.save_spam_report = function(element) {
    $form = $(element).parent().parent();
    if(this.validate_form($form)) {
      $.getJSON(this.base_url+'/saveSpam/'+this.data.session_key+this.json_param, $form.serialize(), function(response){
        $parent = $form.parent();
        $parent.html('<div class="qaw-success">Flagged as spam</div>');
        $parent.fadeOut(2000, function(){
          $parent.remove();
        });
      });
    }
  }

  this.save_vote = function(element, type, element_2) {
    if($(element).hasClass('qaw-vote-greyed') || $(element_2).hasClass('qaw-vote-greyed'))
      return false;
    var $element = $(element);
    var qid = $element.attr('rel');
    $.getJSON(this.base_url+'/saveVote/'+this.data.session_key+'/'+qid+'/'+type+this.json_param, function(response){
      var text = $element.text();
      var cnt = text.replace(/.*\(/, '');
      text = text.replace(cnt, '');
      var closing = '';
      if(cnt.toString().indexOf(')') > -1){
        cnt = cnt.replace(')', '');
        closing = ')';
      }
      $element.text(text+(parseInt(cnt)+1)+closing);
      $element.addClass('qaw-vote-greyed');
      /*  $element.removeClass('qaw-vote-up');
      else
        $element.removeClass('qaw-vote-down');*/
    });
  }

  this.blue_round_section = function(position){
    return '<div class="qawiki-bluish-cor">\
      <div class="qa-left-'+position+'"></div>\
      <div class="qa-right-'+position+'"></div>\
      <div class="qawiki-clear"></div>\
    </div>\
    <div class="qawiki-clear"></div>';
  }

  this.toggle_text = function(element){
    var txt = $(element).text();
    if(txt.indexOf('Hide') > -1) {
      $(element).find('a', 0).text(txt.replace('Hide', 'Show'));
      $(element).removeClass('qa-button-selected');
    } else {
      $(element).find('a', 0).text(txt.replace('Show', 'Hide'));
      $(element).addClass('qa-button-selected');
    }
  }

  this.is_logged_in = function() {
    if(!this.customer_id) {
      alert('Login before you go for it');return false;
      var self = this;
      if(this.data.widget['qa_login_url'] != ''){
        $.get(this.data.widget['qa_login_url'], function(response){
          self.qawiki_html.qawiki_popup(self.qa_helper, response, 500);
        });
      }
      return false;
    }

    return true;
  }

  this.search_popup = function() {
    var self = this;
    $.getJSON(this.base_url + '/search/' + this.store_id + this.json_param, function(response){
      self.qawiki_html.qawiki_popup(self.qa_helper, response, 500);
    });
    /*$.getJSON(this.base_url + '/categories/' + this.store_id + this.json_param, function(response){
      self.qawiki_html.qawiki_popup(self.qa_helper, response, 500);
    });*/
  }
  
  this.browse_products = function() {
    var self = this;
    $.getJSON(this.base_url + '/categories/' + this.store_id + this.json_param, function(response){
      $('#qawiki-popup-content').html(response);
      $('#qaPopup_Desc').text('Navigate through the categories to find a product.');
      /*var node = $('.qawiki-categories').find('.qawiki-cat');
      if(node && node.length > 0){
        $(node[0]).click();
      }*/
    });
  }

  this.products_list = function(element) {
    var params = this.get_linked_products();
    $.getJSON(this.base_url + '/products/' + this.store_id + '/' + $(element).attr('rel') + this.json_param, params, function(response){
      $('#qawiki-products').html(response.products);
      if(typeof response.sub_categories != 'undefined' && !$(element).next().hasClass('qawiki-sub-cat')){
        $(element).after(response.sub_categories);
      }
      $('#qaPopup_Desc').html('Products in <strong>'+$(element).find('a').text()+'</strong>');
    });
  }
  
  this.search_product = function() {
    var search_key = $('#qawiki_psearch').val();
    if(search_key.replace(/\s+/, '') != ''){
      var params = this.get_linked_products();
      $.getJSON(this.base_url + '/search_products/' + this.store_id + '/' + $('#qawiki_psearch').val() + this.json_param, params, function(response){
        if(response != 'failure'){
          $('#qawiki-popup-content').html(response);
          $('#qaPopup_Desc').html('Products that match the term <strong>"'+search_key+'"</strong>');
          
        }
      });
    }
    
    return false;
  }
  
  this.get_linked_products = function(){
    var $container = $('#qawiki-link-prod'+this.popup_link_id);
    var params = 'qawiki_products=';
    $.each($container.parent().find('input[name=qawiki_products[]]'), function(ind, ele){
      params += $(ele).val()+',';
    });
    
    return params;
  }

  this.show_linked_product = function(element) {
    $container = $('#qawiki-link-prod'+this.popup_link_id);
    var pord_cnt = $container.parent().find('input[name=qawiki_products[]]').length;
    if(pord_cnt == 3) {
      return false;
    }
    if($container.length > 0) {
      $(element).removeClass('qawiki-add-product').addClass('qawiki-product-added');
      $(element).find('a', 0).text('Added');
      var content = $(element).prev().text();
      $container.before(this.qawiki_html.linked_product(content, $(element).attr('rel')));
      if(parseInt(pord_cnt) + 1 == 3) {
        $container.hide();
      }
    }
  }

  this.related_products = function(post, type) {
    var html = '';
    if(typeof post.related != 'undefined' && post.related.length > 0) {
      html = '<p><strong>Products from my '+type+'</strong></p>';
      for(var i = 0; i < post.related.length; i++) {
        var tx = post.related[i].title;
        if(post.related[i].url) tx = '<a href="'+post.related[i].url+'" target="_blank">'+tx+'</a>';
        if(post.related[i].image) tx = '<span class="qawiki-relprod-left"><img align="top" src="'+post.related[i].image+'" /></span><span class="qawiki-relprod-img">' + tx + '</span>';
        html += '<p class="qawiki-rel-prod">'+tx+'</p>';
      }
    }

    return html;
  }

  this.designate_badge = function(row) {
    var html = (typeof row.badge_image != 'undefined' && row.badge_image ? ' <img src="'+this.base_url+'/../'+(row.badge_image.indexOf('default_') > -1 ? 'images/badges/' : 'uploads/teams/'+this.data.info.qa_team_id+'/t-')+row.badge_image+'" align="top"/> ' : '') +
      (typeof row.designation != 'undefined' && row.designation ? row.designation : '');

    if(html != '')
      html = '<p><strong>'+html+'</strong></p>';

    return html;
  }
  
  this.check_image_option = function(type){
    if (this.data.info.image_option == 1){
      return false;
    } else if (this.data.info.image_option == 2 && type == 'Answer'){
      return false;
    } else if (this.data.info.image_option == 3 && type == 'Question'){
      return false;
    }
    
    return true;
  }
  
  this.top_contributor = function(row) {
    return (typeof row.top_contributor != 'undefined') ? '<div><span class="qa-top-contributor">Top 25 contributor</span></div>' : '';
  }
  
  this.browseStore = function(){
    $.getJSON(this.base_url + '/browseStore/' + this.data.session_key + '/' + $("#qaw-browse-char").val() + '/' + $("#qaw-browse-type2").val() + this.json_param, function(response){
      $("#qaw-store-browse").html(response.html);
    });
  }
  
  this.get_sub_categories = function(me, category_id) {
    var $parent = $(me).parent().parent();

    $.getJSON(this.base_url + '/subCategories/' + this.data.session_key + '/' + category_id, function(data){
      if($('#qaw-store-browse #qaw-sub-cat-'+category_id).length == 0){
        $parent.after(data.html);
      }
    });
  }

  this.add_link = function(item_title, item_url) {
    var content = $('#qaw-dlg-text').val()+' ['+item_title+(item_url != '' ? '|'+item_url : '')+']';

    $('#qaw-dlg-text').val(content);
  }

  this.add_link_from_suggestion = function() {
    if(this.qa_helper.auto_item && $('#qaw-search-item').val().replace(/\s+/, '') != ''){
      this.add_link(this.qa_helper.auto_item.value, this.qa_helper.auto_item.url);
    }
  }
  
  this.show_answer_dialog = function($target) {
    this.qawiki_html.remove_dialog();
    var qid = $target.parent().attr('rel');
    //self.answer_form.call(self, $target.parent(), qid);
    this.qawiki_html.qawiki_popup(this.qa_helper, this.qaw_dialogs.questionDialog('answer', this.data.answer_dlg, qid), {width: 702, dlg_class:'qaw-dlg-no-bg'});
    var url = this.base_url+"/autoSearch/"+this.data.session_key+"/"+$("#qaw-store-borwse-type").val();
    this.qa_helper.autocomplete_event(url);
  }
  
  this.switch_tab = function($target){
    $target.parent().parent().find('.qaw-selected').removeClass('qaw-selected');
    $target.parent().addClass('qaw-selected');
    this.load_questions($target.attr('rel'), 0);
  }
  
  this.attach_share_options = function(){
    addthis.button(".qaw-share-options");
  }
  
  this.clear_auto_suggestion = function() {
    if(this.qa_helper.auto_item && this.qa_helper.auto_item.value != $('#qaw-search-item').val()){
      this.qa_helper.auto_item = null;
    }
  }
}

/**
 * qawikiHtml class
 *
 */
var qawikiHtml = function(options) {

  this.get_sub_nav = function(){
    $sub_nav = $('<div class="qawiki-sub-header"></div>');
    $sub_nav.append('<div class="qa-sub-heading">Q&amp;A</div>'+
      this.sort_options()+
      '<div class="qawiki-clear"></div>'
    );
      /*<div class="qawiki-sub-links">\
        <ul>\
          <li class="qa-sub-first">View:</li>\
          <li><a href="javascript:;" class="qawiki-selected" rel="all">All</a></li>\
          <li><a href="javascript:;" rel="recent">Recent</a></li>\
          <li><a href="javascript:;" rel="answered">Answered</a></li>\
          <li class="qa-sub-last"><a href="javascript:;" rel="unanswered">Unanswered</a></li>\
        </ul>\
      </div>'
    );*/

    return $sub_nav;
  }

  this.spam_report = function(moderation_type, qid){
    return (parseInt(moderation_type) == 1) ? '<a class="qaw-action qaw-spam" href="javascript:;" rel="'+qid+'"><strong>Flag</strong></a>' : '';
  }

  this.vote_html = function(functions, row, type){
    //alert(functions.toSource());
    var css_class = 'qaw-action';
    if(row.vote_id)
      css_class += ' qaw-vote-greyed';

    var cnt = '';
    if(type == 'up'){
      cnt = (row.pos_vote ? row.pos_vote : 0);
      if(functions.vote_positive_image != ''){
        css_class += ' qaw-vote qaw-pos-vote';
      } else {
        cnt = 'Yes ('+cnt+')';
      }
      css_class += ' qaw-vote-up';
    } else if(type == 'down'){
      cnt = (row.neg_vote ? row.neg_vote : 0);
      if(functions.vote_negative_image != ''){
        css_class += ' qaw-vote qaw-neg-vote';
      } else {
        cnt = 'No ('+cnt+')';
      }
      css_class += ' qaw-vote-down';
    }
    
    return '<a class="'+css_class+'" rel="'+row.qa_post_id+'" href="javascript:;">'+cnt+'</a>';
  }

  this.vote_content = function(functions, self, row){
    var html = '';
    if(self.info.vote_type > 1) {
      html = this.vote_html(functions, row, 'up');
      if(self.info.vote_type > 2) {
        html += ' '+this.vote_html(functions, row, 'down');
      }
      
      html = ' Helpful? '+html;
    }

    return html;
  }

  this.wrap_main = function($child) {
    $('#qaw-widget').append($child.html());
  }

  this.qawiki_popup = function(qawiki_helper, popup_contents, config) {
    if(!config.width) config.width = 300;
    var leftPos = ($('body').width() - config.width) / 2;
    leftPos = Math.round(leftPos.toString().replace('px', ''));
    $('#qaw-widget').append('<div class="qaw-overlay-bg" id="qaw-overlay"></div>\
      <div id="qaw-window" style="left: '+leftPos+'px; width: '+config.width+'px; display: none;">\
        <div id="qaw-overlay-container '+(typeof config.dlg_class != 'undefined' ? config.dlg_class : '')+'">\
          <div>\
            <div class="qaw-dlg-row">\
              <div class="qaw-dlg-head-left"></div>\
              <div class="qaw-dlg-head-mid"><a href="javascript:;" class="qaw-dlg-close"></a></div>\
              <div class="qaw-dlg-head-right"></div>\
            </div>\
          </div>\
          '+popup_contents+'\
          <div class="qaw-dlg-bottom">\
            <div class="qaw-dlg-btm-left"></div>\
            <div class="qaw-dlg-btm-mid"></div>\
            <div class="qaw-dlg-btm-right"></div>\
          </div>\
        </div>\
      </div>'
    );
      
    var scrollTop = $('body').scrollTop();
    var h = qawiki_helper.page_height() - $('#qaw-window').height();
    var topPos = scrollTop + (h > 0 ? h/2 : 0);
    topPos = Math.round(topPos.toString().replace('px', ''));
    //alert(qawiki_helper.page_height());
    $('#qaw-window').css({'top': topPos+'px', 'display': 'block'});
  }

  this.link_products = function(id_){
    return '<div class="qawiki-link-products" id="qawiki-link-prod'+id_+'">\
      <div class="qawiki-add">ADD</div>\
      <div class="qawiki-linked-prod qawiki-search-icon"></div>\
    </div>';
  }

  this.linked_product = function(title, product_id){
    return '<div class="qawiki-link-products">\
      <div class="qawiki-remove">REMOVE</div>\
      <div class="qawiki-linked-prod"><table cellpadding="0" cellspacing="0"><tr><td>'+title+'</td></tr></table></div>\
      <input type="hidden" name="qawiki_products[]" value="'+product_id+'" />\
    </div>';
  }

  this.load_video = function(post){
    if(post.video_url) {
      var startPos = post.video_url.indexOf('v=');
      if(startPos > -1) {
        startPos += 2;
        var video_id = '';
        if(post.video_url.indexOf('&', startPos) > -1) {
          video_id = post.video_url.substring(startPos, post.video_url.indexOf('&', startPos));
        } else {
          video_id = post.video_url.substr(startPos, post.video_url.length - 1);
        }
        return '<div class="qawiki-video">\
            <p class="qawiki-pl0"><strong>Video related to my answer</strong></p>\
            '+(post.video_caption ? '<p>'+post.video_caption+'</p>' : '')+'\
            <p><iframe title="'+(post.video_caption ? post.video_caption : '')+'" width="240" height="210" src="http://www.youtube.com/embed/'+video_id+'" frameborder="0" allowfullscreen="false"></iframe></p>\
          </div>';
      }
    }

    return '';
  }

  this.search_section = function (){
    return '<div class="qawiki-search">\
      <input class="qawiki-input-text qawiki-search-text" name="qawiki_search_text" id="qawiki_search_text" />\
      <input class="qawiki-search-button" id="qawiki_search" type="button" value="GO" />\
    </div>\
    <div class="qawiki-clear"></div>\
    <div style="display:none" id="qawiki-search-result"></div>';
  }
  
  this.sort_options = function(){
    return '<select class="qawiki-sort-options" name="qawiki-sort-options">\
      <option value="helpful">Questions With the Most Helpful Answers</option>\
      <option value="recentq">Most Recent Questions</option>\
      <option value="oldestq">Oldest Questions </option>\
      <option value="recenta">Questions With Most Recent Answers </option>\
      <option value="oldesta">Questions With Oldest Answers </option>\
      <option value="answers">Questions With Most Answers </option>\
      <option value="noanswers">Can You Answer These Questions? </option>\
    </select>';
  }
  
  this.remove_dialog = function() {
    $('#qaw-window').remove();
    $('#qaw-overlay').remove();
  }
}

var qawikiUtil = function(){
  this.queryString = '';
  
  this.processParams = function(url, param_exist) {
    var queryString = url.replace(/^[^\?]+\??/,'');
    var params = this.parseQuery(queryString);

    if(param_exist){
      if(params && typeof params[param_exist] != 'undefined'){
        params = params[param_exist];
        if(params.indexOf('#') > -1){
          params = params.substr(0, params.indexOf('#'));
        }
      } else {
        params = false;
      }
    }
    
    return params;
  }
  
  this.processScriptPath = function(){
    var qawScript = null;
    $.each($('script'), function(index, element){
      if(!qawScript && $(element).attr('src') && $(element).attr('src').toString().indexOf('widget_new.js') > -1) {
        qawScript = $(element).attr('src');
      }
    });

    this.queryString = qawScript.replace(/^[^\?]+\??/,'');
    var params = this.parseQuery(this.queryString);

    var index = qawScript.indexOf('/js/', '');
    params['base_url'] = qawScript.substr(0, index + 1)+'widget_new';
    
    if(this.queryString.length > 0)
      this.queryString = '&'+this.queryString;
    
    return params;
  }

  this.parseQuery = function(query) {
    var Params = new Object ();
    if ( ! query ) return Params; // return empty object
    var Pairs = query.split(/[;&]/);
    for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
    }
    return Params;
  }
}

var qawDialogs = function() {
  
  this.questionDialog = function(type, settings, question_id) {
    var functions = settings.functions;
    
    var charOpt = '';
    for(var j=65; j<91; j++){
      charOpt += '<option value="'+String.fromCharCode(j)+'">'+String.fromCharCode(j)+'</option>';
    }
    
    return '<form id="qaw-form-qa">\
      <div class="qaw-dlg-content" id="qaw-qa-dlg-cont">\
        <div>\
          <div class="qaw-heading">'+settings.title+'</div>\
          <div class="qaw-sub-heading">'+settings.sub_title+'</div>\
        </div>\
        <div style="display:none" id="qaw-qa-saved" class="qaw-msg-box">Your '+type+' has been saved. <div class="qaw-msg-close"><a onclick="$(\'#qaw-qa-saved\').hide()" href="javascript:;"></a></div></div>\
        <div class="qaw-dlg-textarea"><textarea id="qaw-dlg-text" name="qaw_text_qa" class="qaw-required"></textarea></div>\
        <a id="qaw-save-qa" href="javascript:;" class="qaw-flex-button qaw-clearfix qaw-dlg-btn-save">\
          <span class="qaw-flex-button-left"></span>\
          <span class="qaw-flex-button-mid">Save</span>\
          <span class="qaw-flex-button-right"></span>\
        </a>\
        <div class="qaw-clear"></div>\
        '+(functions.categories == 'on' || functions.brands == 'on' || functions.products == 'on' ?
        '<div id="qaw-auto-items-panel">\
          <div class="qaw-dlg-add-panel qaw-clearfix">\
            <div class="qaw-dlg-lb-add">Add</div>\
            <select id="qaw-store-borwse-type" class="qaw-dlg-add">\
              '+(functions.categories == 'on' ? '<option value="categories">Categories</option>' : '')+'\
              '+(functions.brands == 'on' ? '<option value="brands">Brands</option>' : '')+'\
              '+(functions.products == 'on' ? '<option value="products">Products</option>' : '')+'\
            </select>\
            <input type="text" id="qaw-search-item" name="qaw-search-item" value="" onblur="qaw_widget.clear_auto_suggestion()" />\
            <label style="display: none" id="no_record">No Records Found</label>\
            <input class="qaw-btn-add-link qaw-fr" value="" type="button" onclick="qaw_widget.add_link_from_suggestion()" />\
            <div class="qaw-clear"></div>\
          </div>\
          <div class="qaw-dlg-browse-panel">\
            <div class="qaw-dlg-add-panel qaw-clearfix">\
              <div class="qaw-dlg-lb-add">Browse </div>\
              <select id="qaw-browse-type2" onchange="qaw_widget.browseStore()" class="qaw-dlg-add">\
                <option value="">Select</option>\
                '+(functions.products == 'on' ? '<option value="products">Products</option>' : '')+'\
                '+(functions.categories == 'on' ? '<option value="categories">Categories</option>' : '')+'\
                '+(functions.brands == 'on' ? '<option value="brands">Brands</option>' : '')+'\
              </select>\
              <div class="qaw-dlg-lb-add">by name</div>\
              <select id="qaw-browse-char" onchange="qaw_widget.browseStore()" class="qaw-dlg-add">\
                <option value="">Select</option>\
                '+charOpt+'\
              </select>\
              '+(type == 'answer' ? '<input type="hidden" name="qaw_question_id" value="'+question_id+'" />' : '')+'\
              <div class="qaw-clear"></div>\
            </div>\
            <div id="qaw-store-browse"></div>\
          </div>\
          <div class="qaw-clear"></div>\
        </div>' : '')+'\
        <div class="qaw-clear"></div>\
      </div>\
    </form>';
  }
}

/**
 * verify embed params, render widget
 *
 */
document.write('<div id="qaw-widget"></div>');

if(typeof qawiki_owner_id == 'undefined') qawiki_owner_id = '';
if(typeof qawiki_id == 'undefined') qawiki_id = '';
if(typeof qawiki_store_id == 'undefined') qawiki_store_id = '';
if(typeof qawiki_type == 'undefined') qawiki_type = '';
if(typeof qawiki_customer_id == 'undefined') qawiki_customer_id = null;

if($('#qaw-widget').length > 0)
{
  var qaw_widget = new qawikiWidget({
    owner_id: qawiki_owner_id,
    store_id: qawiki_store_id,
    item_id: qawiki_id,
    item_type: qawiki_type,
    customer_id: qawiki_customer_id,
    customer_email: (typeof qawiki_customer_email != 'undefined' ? qawiki_customer_email : 'null')
  }).load();
}