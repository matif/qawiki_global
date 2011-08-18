<?php

$CI =& get_instance();

$custom_config = array();

$custom_config['appearance_save_edit_url'] = base_url() . 'settings/saveAppearanceConfig/'. $CI->store_id;
$custom_config['email_save_edit_url'] = base_url() . 'settings/saveEmail/'.$CI->store_id."/".$CI->uri->segment(2);
$custom_config['db_fields'] = array(
  'title',
  'sub_title',
  'from_email',
  'button_text',
  'email_footer'
);

// buttons
$custom_config['buttons'] = array(
  'ask_question'    => 'qaw-ask-question',
  'answer_it'       => 'qaw-answer-it',
  'search_button'   => 'qaw-search-btn',
  'thank_you'       => 'qaw-thank-you-btn',
  'contributor'     => 'qaw-contributor-close'
);

// default colors
$custom_config['avatar_color'] = 'FF0000';
$custom_config['button_color'] = '000000';

$custom_config['recent_tag_contents'] = array(
  "5_questions_within_10_days"      =>  "5 Questions within 10 days",
  "10_questions_within_15_days"     =>  "10 Questions within 15 days",
  "10_questions_within_one_month"   =>  "10 Questions within one month"
);

// CSS
$default_embed_code = default_embed_code();

$custom_config['default_button'] = $default_embed_code['default_button'];

$custom_config['tags'] = array(
    "#UserName",
    "#Product",
    '#Question',
    "#JoinDate",
    "#VoteSource",
    "#Date/Time",
    "#Email",
    "#Q&AEmail",
    "#QA_Address"
);
// Appearance page default
$custom_config['appearance_default'] = array(
  'title'             =>   '#Product Q&A',
  'question_button'   =>   'Question',
  'popular_tab'       =>   'Popular',
  'tabs'              =>   array(
    'recent_tab'        =>   'Recent',
    'unanswered_tab'    =>   'Unanswered',
    'popular_tab'       =>   'Popular',
  ),
  'answer_button'     =>   'Answer',
  'search_button'     =>   'Search'
);

// contributor page defaults
$custom_config['contributor_default'] = array(
  'title'             => 'Dear #UserName',
  'contributor_btn'   => 'close'
);

// Question/Answer page default
$custom_config['question_default'] = array(
  'title'             =>   'Ask A Question',
  'sub_title'         =>   'Ask Your Question Below.'
);

$custom_config['answer_default'] = array(
  'title'             =>   'Answer',
  'sub_title'         =>   '#Question'
);

$custom_config['default_css'] = '
#qaw-widget {font-family: '.$default_embed_code['font_family'].'; color: #'.$default_embed_code['font_color'].'; width: '.$default_embed_code['width'].'px; font-size:12px}
#qaw-widget input, #qaw-widget form, #qaw-widget table, #qaw-widget div, #qaw-widget p, #qaw-widget textarea, #qaw-widget a {
  '.$default_embed_code['font_family'].'
}
#qaw-widget a, #qaw-widget a:visited, #qaw-widget a:hover {color: #'.$default_embed_code['link_color'].'}
#qaw-widget a.qaw-action, #qaw-widget a.qaw-action:visited, #qaw-widget a.qaw-action:hover {color: #'.$default_embed_code['link_color'].'}
.qawiki-text {text-decoration: underline}
a.qawiki-action {color: #'.$default_embed_code['action_text_color'].'; text-align: right}
a.qawiki-action:hover {color: #'.$default_embed_code['action_text_color'].'}
a.qawiki-action:visited {color: #'.$default_embed_code['action_text_color'].'}
a.qaw-button{color:#636363; display:block; float:left; font-family:'.$default_embed_code['font_family'].'; font-size:11px; font-weight:bold; height:23px; text-decoration:none}
a.qaw-button span{display:block; line-height:23px; padding:0 8px 0px 2px; margin-left:6px}
a.qaw-button:hover{background-position:0 -46px; outline:none;}
a.qaw-button:hover span{background-position:right -69px;}
a.qaw-buton-gray{background:transparent url('.base_url().'images/frontend/btn_gray.png) no-repeat scroll 0 0}
a.qaw-buton-gray span{background:transparent url('.base_url().'images/frontend/btn_gray.png) no-repeat right -23px}
a.qaw-buton-pink{background:transparent url('.base_url().'images/frontend/btn_pink.png) no-repeat scroll 0 0}
a.qaw-buton-pink span{background:transparent url('.base_url().'images/frontend/btn_pink.png) no-repeat right -23px}
a.qaw-buton-yellow{background:transparent url('.base_url().'images/frontend/btn_yellow.png) no-repeat scroll 0 0}
a.qaw-buton-yellow span{background:transparent url('.base_url().'images/frontend/btn_yellow.png) no-repeat right -23px}
  
.qaw-user-avatar {width: 32px;height: 32px;float: left;border: 1px solid #E2AFAF;margin: 0 10px 0 0;background: #'.$custom_config['avatar_color'].'}
.qaw-contributor-header {width: '.$default_embed_code['width'].'px;margin: 0 0 10px 0;color: #000000}
.qaw-thank-you-header {width: '.$default_embed_code['width'].'px;margin: 0 0 10px 0;color: #000000}
.qaw-search-text{width:200px; margin-right:8px; float:left; vertical-align: middle}
';

$CI->custom_config = $custom_config;

