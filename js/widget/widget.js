
/**
 * qawikiHelper class
 * 
 */
var qawikiHelper = function() {
  this.answers_count = {};

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
      return '<p class="qawiki-see-more"><a href="javascript:;" onclick="qawiki_widget.load_answers(this,'+offset+')" rel="'+rel[0]+'/'+rel[1]+'/'+rel[2]+'">see more</a></p>';

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
  this.popup_link_id = null;

  /* functions */
  this.load = function(){
    var self = this;
    var navigate_to_post = this.qawiki_util.processParams(window.location.href, 'qa_post_id');
    $.getJSON(this.base_url + '/main/' + this.owner_id + '/' + this.store_id + '/' + this.item_id + '/' + this.item_type + '/' + this.customer_id + '/' + navigate_to_post + this.json_param, {qce : this.customer_email}, function(response){
      self.data = eval(response);
      self.render.call(self);
    });

    self.attach_events();

    return this;
  }

  this.attach_events = function(){
    var self = this;
    $('#qawiki-widget').live('click', function(e){
      var $target = $(e.target);

      // parent checks
      if($target.hasClass('qawiki-add') || $target.hasClass('qawiki-linked-prod')) {
        $target = $target.parent();
      } else if($target.parent().hasClass('qawiki-cat')){
        $target = $target.parent();
      }

      // expand question
      if($target.parent().hasClass('qa-answer-count')){
        $target = $target.parent().parent();
        if($target.find('.qawiki-expand').length > 0){
          $target = $target.find('.qawiki-expand', 0);
        } else {
          $target = $target.find('.qawiki-collapse', 0);
        }
      }
      if($target.hasClass('qawiki-expand')) {
        self.load_answers($target, 0);
      } else if($target.hasClass('qawiki-collapse')) {
        var qid = $target.attr('rel').split('/');
        $parent = $target.parent().parent();
        $container = $parent.find('#qawiki-answer'+qid[2]);
        $container.slideUp(300);
        $target.attr('class', 'qawiki-expand');
      
      } else if($target.parent().hasClass('qawiki-add-answer')) { // add answer
        if(!self.is_logged_in()) {
          return false;
        }
        var qid = $target.parent().attr('rel');
        self.answer_form.call(self, $target.parent(), qid);

      } else if($target.parent().attr('id') == 'qaAskQuest') { // ask question
        if(!self.is_logged_in()) {
          return false;
        }
        self.toggle_text($target.parent());
        self.popup_link_id = 0;
        $('#qaQuestForm').toggle();

      } else if($target.parent().attr('id') == 'qaQuestAns') { // hide-show list
        self.toggle_text($target.parent());
        $('#qawikiContent').toggle();
        
      } else if($target.parent().parent().parent().hasClass('qawiki-sub-links')) { // sub links
        $('.qawiki-sub-links a').removeClass('qawiki-selected');
        $target.addClass('qawiki-selected');
        self.load_questions.call(self, $target.attr('rel'));

      } else if($target.parent().hasClass('qawiki-save-answer')){ // save answer
        self.save_post($target.parent(), 'Answer');

      } else if($target.parent().attr('id') == 'qawikiSaveQuest') { // save question
        self.save_post($target.parent(), 'Question');

      } else if($target.parent().hasClass('qawiki-spam') || $target.hasClass('qawiki-spam-cancel')) { // spam show, cancel
        if(!self.is_logged_in()) {
          return false;
        }
        if(!$target.hasClass('qawiki-spam-cancel'))
          $target = $target.parent();
        self.report_spam_form($target);

      } else if($target.parent().hasClass('qawiki-spam-save')) { // spam save
        self.save_spam_report($target.parent());
        
      } else if($target.hasClass('qawiki-thumbs-up')) { // thumbs up
        if(!self.is_logged_in()) {
          return false;
        }
        self.save_vote($target, 'up');

      } else if($target.hasClass('qawiki-thumbs-down')) { // thumbs down
        if(!self.is_logged_in()) {
          return false;
        }
        self.save_vote($target, 'down');

      } else if($target.hasClass('qawiki-link-products')) { // link products
        self.popup_link_id = $target.attr('id').replace('qawiki-link-prod', '');
        self.search_popup();

      } else if($target.hasClass('qawiki-cat')){ // categories list
        $('.qawiki-cat').removeClass('qawiki-cat-selected');
        $target.addClass('qawiki-cat-selected');
        self.products_list($target);
        
      } else if($target.parent().hasClass('qa-popup-close')){ // popup close
        $('#qawiki-window').remove();
        $('#qawiki-overlay').remove();
        
      } else if($target.parent().hasClass('qawiki-add-product')){ // add product
        self.show_linked_product($target.parent());

      } else if($target.hasClass('qawiki-remove')){ // remove product
        var $parent = $target.parent();
        $parent.parent().find('.qawiki-link-products', 0).show();
        $parent.remove();

      } else if($target.attr('id') == 'qawiki_search') {
        //$('.qawiki-sub-links a[rel=all]').click();
        self.load_questions.call(self);
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
        $('#qawiki-widget').text('Email is not configured');
        return false;
      }
      this.permission = this.data.info.qa_permission;
      $container = $('<div></div>');
      $container.append('<div class="qa-wtop"><div class="qa-wtop-left"></div><div class="qa-wtop-right"></div></div>');
      $container.append(this.item_info());
      $container.append('<div class="qa-wbottom"><div class="qa-wbottom-left"></div><div class="qa-wbottom-right"></div></div>');
      this.qawiki_html.wrap_main($container);
    }
  }

  this.item_info = function() {
    $item = $('<div class="qa-wcenter"></div>');
    $item.append('<div class="qawiki-heading"><h2>'+this.data.info['qa_store_name']+'</h2>\
      <ul class="qawiki-reviews">\
        <!--li class="first"><a href="javascript:;">'+this.data.customers_count+' customer Q&amp;A posts</a></li-->\
        <li class="first"><a href="javascript:;">More about Q&amp;A</a></li>\
      </ul>\
    </div>');

    // share
    $item.append(this.qawiki_html.share_content());
    // clear
    $item.append('<div class="qawiki-clear"></div>');
    // icon img and item details
    $item.append(this.header_section());
    // contents
    $content = $('<div id="qawikiContent"></div>');
    $content.append(this.qawiki_html.get_sub_nav());
    //$content.append(this.qawiki_html.sort_options());
    //$content.append('<div class="qawiki-clear"></div>');
    $content.append(this.qawiki_html.search_section());
    $content.append(this.questions_list(this.data));

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

  this.questions_list = function(data){
    $questions = $('<div class="qawiki-content"></div>');
    if(data.questions) {
      var rows = data.questions;
      for(var i = 0; i < rows.length; i++) {
        this.qa_helper.set_answer_count(rows[i].qa_post_id, rows[i].total_answers, 0);
        $questions.append('<div class="qawiki-blue-corners">\
            ' + this.blue_round_section('top') + '\
            <div class="qawiki-bluish-content">\
              <h2>\
                <span class="qawiki-expand" rel="'+rows[i].qa_ref_id+'/'+rows[i].qa_post_type+'/'+rows[i].qa_post_id+'"></span><span>'+rows[i].qa_title+'</span>\
                <span class="qa-answer-count"><a href="javascript:;">'+rows[i].total_answers+' answer'+(parseInt(rows[i].total_answers) > 1 ? 's' : '')+'</a></span>\
              </h2>\
              '+(this.permission == 'both' ? '<div class="qawiki-add-answer" rel="'+rows[i].qa_post_id+'"><a href="javascript:;"></a></div>' : '')+'\
              <div class="qawiki-clear"></div>\
              '+this.designate_badge(rows[i])+'\
              <p><strong>'+(rows[i].image_url ? '<img src="'+this.base_url+'/../uploads/stores/'+this.store_id+'/t-'+rows[i].image_url+'" align="top"/> ' : '')+'<span class="qawiki-details">'+rows[i].qa_description+'</span></strong></p>\
              <p class="qawiki-time"><strong>'+rows[i].qa_created_at+'<br/>by: '+rows[i].name+'</strong> '+'</p>\
              <p>'+this.qawiki_html.vote_content(this.data, rows[i])+(this.data.info.vote_type > 1 && this.data.info.moderation_type == 1 ? ' | ' : '')+
              this.qawiki_html.spam_report(this.data.info.moderation_type, rows[i].qa_post_id)+'</p>\
              '+this.related_products(rows[i], 'question')+'\
            </div>\
            ' + this.blue_round_section('bottom') + '\
          </div>'
        );
      }

      $questions.append('<div class="qawiki-pagination">'+data.pagination+'</div>');
    }

    return $questions;
  }

  this.answers_list = function(rows, rel, offset) {
    var html = (offset == 0) ? '<h3><strong>Answers</strong></h3>' : '';
    if(rows.length > 0) {
      for(var i = 0; i < rows.length; i++) {
        html += '<div class="qawiki-answer">\
          '+this.designate_badge(rows[i])+'\
          <h4><span class="qa-ans-head">A:&nbsp;</span>'+rows[i].qa_title+'</h4>\
          <p>'+(rows[i].image_url ? '<img src="'+this.base_url+'/../uploads/stores/'+this.store_id+'/t-'+rows[i].image_url+'" align="top"/> ' : '')+'<span class="qawiki-details">'+rows[i].qa_description+'</span></p>\
          <p class="qawiki-user-name">'+rows[i].qa_created_at+'<br/>by: '+rows[i].name + '</p>\
          '+this.top_contributor(rows[i])+'\
          <p>'+this.qawiki_html.vote_content(this.data, rows[i])+(this.data.info.vote_type > 1 && this.data.info.moderation_type == 1 ? ' | ' : '')+
          this.qawiki_html.spam_report(this.data.info.moderation_type, rows[i].qa_post_id, 'qawiki-f11')+'</p>\
          '+this.related_products(rows[i], 'answer')+'\
          '+this.qawiki_html.load_video(rows[i])+'\
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
    if($('#qawiki_spam'+qid).length > 0) {
      $('#qawiki_spam'+qid).slideUp(300, function(){
        $('#qawiki_spam'+qid).remove();
      });
      return false;
    }

    $parent = $(element).parent();
    $parent.after('<div id="qawiki_spam'+qid+'" style="display:none">\
        <form action="">\
          <h3>Describe the issue:</h3>\
          <textarea class="qawiki-input-text qawiki-required" name="qawiki_issue"></textarea>\
          <div class="qawiki-button-section">\
            <div class="qawiki-button qawiki-spam-save"><a href="javascript:;">Submit</a></div>\
            <div class="qawiki-button"><a href="javascript:;" rel="'+qid+'" class="qawiki-spam-cancel">Cancel</a></div>\
          </div>\
          <input type="hidden" name="qawiki_post_id" value="'+qid+'" />\
          <div class="qawiki-clear"></div>\
        </form>\
      </div>'
  );

    $('#qawiki_spam'+qid).slideDown(300);
  }

  this.load_questions = function(type, offset){
    if(!offset)
      offset = 0;
    this.list_filter = type;
    var self = this;
    var key = $('#qawiki_search_text').val();
    $.getJSON(this.base_url + '/questions/' + this.data.session_key + '/' + type + '/' + offset + '/' + key + this.json_param, function(response){
      if(key.replace(/\s+/, '') == ''){
        $('#qawiki-search-result').text('').hide();
      } else {
        $('#qawiki-search-result').text('Search results for "'+key+'"').show();
      }
      response = eval(response);
      response = response[0];
      self.qa_helper.reset_count();
      $('#qawikiContent .qawiki-content').remove();
      $('#qawikiContent').append(self.questions_list.call(self, response));
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
      $container = $('#qawiki-answer'+qid);
      if($container.length == 0 && offset == 0) {
        $container = $('<div id="qawiki-answer'+qid+'"></div>');
        $(self_expand).parent().parent().append($container);
      }
      if(offset == 0) {
        $container.hide();
        $container.html(self.answers_list.call(self, response, $(self_expand).attr('rel'), offset));
        $container.slideDown(300);
      } else {
        $container.append(self.answers_list.call(self, response, $(self_expand).attr('rel'), offset));
        $(self_expand).parent().remove();
      }
      
      $(self_expand).attr('class', 'qawiki-collapse');
    });
  }

  this.validate_form = function(form) {
    var valid = true;
    var self = this;
    var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
    $.each($(form).find('.qawiki-required'), function(index, element){
      var is_empty = ($(element).val().replace(/\s+/, '') == '');
      var error_msg = 'You must enter this field.';
      var is_invalid = false;
      if(!is_empty && $(element).hasClass('qawiki-email') && !regExp.test($(element).val())) {
        error_msg = 'Email is not valid.';
        is_invalid = true;
      } else if($(element).hasClass('qawiki-file')) {
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
        if(!$(element).next().hasClass('qawiki-error')) {
          $(element).after('<div class="qawiki-error">'+error_msg+'</div>');
        } else {
          $(element).next().text(error_msg);
        }
        valid = false;
      } else {
        if($(element).attr('name') && $(element).attr('name').indexOf('nickname') > -1)
          self.data.nick_name = $(element).val();
        if($(element).next().hasClass('qawiki-error'))
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

  this.save_post = function(element, type) {
    $form = $(element).parent().parent();
    var self = this;
    if(this.validate_form($form)) {
      if(this.check_image_option(type)){
        $form.attr('action', this.base_url+'/savePost/'+this.data.session_key+'/'+type.toLowerCase());
        $form.submit();
        return false;
      }
      $.getJSON(this.base_url+'/savePost/'+this.data.session_key+'/'+type.toLowerCase()+this.json_param, $form.serialize(), function(response){
        if(response == 'spam') {
          if(!$(element).prev().hasClass('qawiki-error'))
            $(element).before('<span class="qawiki-error">Content contains obnoxious words</span>');
          return false;
        }
        $parent = $form.parent();
        if(type == 'Answer') {
          $parent.html('<div class="qawiki-success">'+type+' posted successfully.</div>');
          $parent.fadeOut(1000, function(){
            $parent.remove();
          });
        } else {
          $('#qaAskQuest').click();
          self.clear_form($form);
          $form.find('.qawiki-floating').html(self.qawiki_html.link_products(0));
        }
      });
    }
  }

  this.save_spam_report = function(element) {
    $form = $(element).parent().parent();
    if(this.validate_form($form)) {
      $.getJSON(this.base_url+'/saveSpam/'+this.data.session_key+this.json_param, $form.serialize(), function(response){
        $parent = $form.parent();
        $parent.html('<div class="qawiki-success">Flagged as spam</div>');
        $parent.fadeOut(2000, function(){
          $parent.remove();
        });
      });
    }
  }

  this.save_vote = function(element, type) {
    var qid = $(element).attr('rel');
    $.getJSON(this.base_url+'/saveVote/'+this.data.session_key+'/'+qid+'/'+type+this.json_param, function(response){
      $(element).text(parseInt($(element).text()) + 1);
      $parent = $(element).parent();
      if($parent.find('.qawiki-thumbs-up').length > 0)
        $parent.find('.qawiki-thumbs-up', 0).removeClass('qawiki-thumbs-up').addClass('qawiki-greyed-up');
      if($parent.find('.qawiki-thumbs-down').length > 0)
        $parent.find('.qawiki-thumbs-down', 0).removeClass('qawiki-thumbs-down').addClass('qawiki-greyed-down');
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
      //alert('Login before you go for it');
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
}

/**
 * qawikiHtml class
 *
 */
var qawikiHtml = function(options) {
  this.share_content = function() {
    $share = $('<div class="qawiki-share"></div>');
    $share.append('<div class="qawiki-fb-like">\
        <iframe src="http://www.facebook.com/plugins/like.php?href='+encodeURIComponent(window.top.location)+'&amp;layout=standard&amp;show_faces=true&amp;width=320&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:320px; height:23px;" allowTransparency="true"></iframe>\
      </div>\
      <div class="qawiki-clear"></div>\
      <div class="qawiki-share-pane">\
        <div class="addthis_toolbox addthis_default_style">\
          <a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4d7a083268c64e1b" class="addthis_button_compact">Share</a>\
          <span class="addthis_separator">|</span>\
          <a class="addthis_button_preferred_1"></a>\
          <a class="addthis_button_preferred_11"></a>\
          <a class="addthis_button_preferred_2"></a>\
          <a class="addthis_button_preferred_3"></a>\
        </div>\
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4d7a083268c64e1b"></script>\
      </div>');

    return $share;
  }

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

  this.spam_report = function(moderation_type, qid, css_class){
    return (parseInt(moderation_type) == 1) ? '<a href="javascript:;" class="qawiki-spam '+(css_class ? css_class : '')+'" rel="'+qid+'"><strong>Flag as Spam</strong></a>' : '';
  }

  this.vote_html = function(row, type){
    var css_class = 'qawiki-thumbs-'+type;
    if(row.vote_id)
      css_class = 'qawiki-greyed-'+type;

    var cnt = 0;
    if(type == 'up' && row.pos_vote)
      cnt = row.pos_vote;
    else if(type == 'down' && row.neg_vote)
      cnt = row.neg_vote;
    return '<span class="'+css_class+'" rel="'+row.qa_post_id+'">'+cnt+'</span>';
  }

  this.vote_content = function(self, row){
    var html = '';
    if(self.info.vote_type > 1) {
      html = this.vote_html(row, 'up');
      if(self.info.vote_type > 2) {
        html += ' '+this.vote_html(row, 'down');
      }
    }

    return html;
  }

  this.wrap_main = function($child) {
    $container = $('<div class="qawiki-blue-box"></div>');
    $container.append('<div class="qa-top"><div class="qa-top-left"></div><div class="qa-top-right"></div></div>');
    $container.append('<div class="qa-center-left"><div class="qa-center-right"><div class="qa-center"><div class="qawiki-white-box">'+$child.html()+'</div><div class="qawiki-clear"></div></div></div></div>');
    $container.append('<div class="qa-bottom"><div class="qa-bottom-left"></div><div class="qa-bottom-right"></div></div><div class="qawiki-clear"></div>');
    
    $('#qawiki-widget').append($container);
  }

  this.qawiki_popup = function(qawiki_helper, popup_contents, p_width) {
    if(!p_width) p_width = 300;
    var scrollTop = $('body').scrollTop();
    var topPos = scrollTop + ((qawiki_helper.page_height() - 100) / 2);
    var leftPos = ($('body').width() - p_width) / 2;
    topPos = Math.round(topPos.toString().replace('px', ''));
    leftPos = Math.round(leftPos.toString().replace('px', ''));
    $('#qawiki-widget').append('<div class="qawiki-overlay-bg" id="qawiki-overlay"></div>\
      <div id="qawiki-window" style="top: '+topPos+'px; left: '+leftPos+'px; width: '+p_width+'px; display: block;">\
        <div id="qawiki-overlay-container">\
          '+popup_contents+'\
          <span class="qa-popup-close"><a href="javascript:;">close</a></span>\
        </div>\
      </div>'
    );
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
}

var qawikiUtil = function(){
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

/**
 * verify embed params, render widget
 *
 */
document.write('<div id="qawiki-widget"></div>');

if(typeof qawiki_owner_id != 'undefined' && typeof qawiki_id != 'undefined' && typeof qawiki_store_id != 'undefined' && typeof qawiki_type != 'undefined')
{
  var qawiki_base_url = 'http://qawiki.iserver.purelogics.info/widget';
  var qawiki_customer_id = typeof qawiki_customer_id != 'undefined' ? qawiki_customer_id : null;

  if($('#qawiki-widget').length > 0)
  {
    var qawiki_widget = new qawikiWidget({
      base_url: qawiki_base_url,
      owner_id: qawiki_owner_id,
      store_id: qawiki_store_id,
      item_id: qawiki_id,
      item_type: qawiki_type,
      customer_id: qawiki_customer_id,
      customer_email: (typeof qawiki_customer_email != 'undefined' ? qawiki_customer_email : '')
    }).load();
  }
}