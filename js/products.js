$(document).ready(function(e) {
  $('.sub_categeries').live('click',function(){
    rel = $(this).attr('rel');      
    rel = rel.split(":|:");
    var self = this;
    $.post(base_url+'ajax/listSubCategory/'+rel[0]+"/"+rel[1],function(data){
        
      $parent = $(self).parent().parent();
      $container = $parent.next();
        
      if($container && $container.attr('class') == 'subcategories-container') {
        $container.slideUp("fast");
        $container.remove();
      }else{
        $container = $('<tr class="subcategories-container"></tr>');          
        $container.html('<td colspan="10" width="100%">'+data+'</td>');
          
        $parent.after($container);
        $container.find('.subcategories-container').slideDown("fast");
      }
    });
  });
  $('.delete').live('click',function(){
    rel = $(this).attr('rel');
    rel = rel.split(":|:");      
    var res = confirm('Are you sure, you want to delete this ?');
    if(res) {
      $.post(base_url+'post/deletePost/'+rel[2]+"/"+rel[0],function(){
        window.location.reload();
      });
    }
  });
  $('.editPost').live('click',function()
  {
    rel = $(this).attr("rel");
    rel = rel.split(":|:");      
    var self = this;      
    $parent = $(this).parent().parent();
    $container = $parent.next();
    if($container && $container.attr('class') == 'question-container') {
      $container.slideUp("fast");
      $container.remove();
    }else{
      $container = $('<tr class="question-container"></tr>');
      if(rel[3] == 'question')
        $container.html('<td colspan="10" width="100%">'+get_question_box(store_id, rel[0], rel[1],rel[2])+'</td>');
      else if(rel[3] == 'answer')                                                           
        $container.html('<td colspan="10" width="100%">'+get_answer_box(store_id, rel[0], rel[1],rel[4],rel[2])+'</td>');
      $parent.after($container);
      $container.find('.question-container').slideDown("fast");
    }
      
  });
  $('.edit').live('click',function(){
    rel = $(this).attr("rel");
    rel = rel.split(":|:");
    data = $('#editForm_'+rel[1]).serialize();      
    var self = this;
    if(validate_form($('#editForm_'+rel[1])))
    {
      $.post(base_url+'post/updateCategoryBrand/'+rel[0]+'/'+rel[1],data, function(data){
        $parent = $(self).parent().parent().parent().parent();
        $parent.slideUp('fast');
        $parent.remove();
      });
    }      
  });
  $('#automaiton').click(function() {
    getMoreProducts(base_url+'post/showProduct/'+store_id+'/ajaxProduct');
  });

  $('.view-details').live('click', function(){
    $(this).next().toggle();
    if($(this).text().indexOf('view') > -1)
      $(this).text('(hide details)');
    else
      $(this).text('(view details)');
  });

  $('.expand-list').live('click', function(){
    if($(this).text().indexOf('Expand') > -1) {
      $(this).text('Collapse All');
      $(this).parent().parent().find('.post-details').show();
    } else {
      $(this).text('Expand All');
      $(this).parent().parent().find('.post-details').hide();
    }
  });
    
  $('.deleteGrp').live('click' ,function()
  {
    rel = $(this).attr('rel');
    $.post(base_url+'post/deleteGrp/'+rel, function(){
      window.location.reload();
    });
  });

  $('.viewGrp').live('click' ,function()
  {
    rel = $(this).attr('rel');
    var html ='';
    $.post(base_url+'post/getProducts/'+rel, function(data){
      html+='<table cellpadding="0" cellspacing="0" width="100%" class="stores-list" border="1px">\
        <thead>\
          <tr>\
            <th><strong>Product Code</strong></th>\
            <th><strong>Product Title</strong></th>\
            <th><strong>Product Description</strong></th>\
          </tr>\
        </thead>';        
      data = eval(data);        
      for(var i = 0; i < data.length;i++){          
        html += '<tr>';
        html += '<td>';
        html += data[i].qa_product_id;
        html += '</td>';
        html += '<td>';
        html += data[i].qa_product_title;
        html += '</td>';
        html += '<td>';
        html += data[i].qa_product_description;
        html += '</td>';
        html+='</tr>';
      }
      html+='<table>'
      $('#showGroupProducts').html(html);
    });
  });     

  $('#makegroup').bind('click', function(){
    if(!product_selected()) {
      $('#group-error').html('Select at least one product').css('display', 'inline-block');
      $('#listGrp').slideUp();
      return false;
    }

    $('#group-error').html('').css('display', 'none');
    $('#listGrp').slideDown();
    $('#list_group').val('new_group');
    $('#list_group').change();
  });

  /* GROUPS HANDLING */
  $('#list_group').bind('change', function(){
    save_product_group(this);
  });

  $('#makeGroup').bind('click', function(){
    save_product_group($('#list_group'), true);
  });

  var cur_add_question = '';

  $('.question').live('click', function(){      
    var rel = $(this).attr('rel');
    cur_add_question = rel;
    rel = rel.split(':|:')
    $parent = $(this).parent().parent();
    $container = $parent.next();
    if($container && $container.attr('class') == 'question-container') {
      $container.slideUp("fast");
      $container.remove();
    }else {
      $container = $('<tr class="question-container"></tr>');
      $container.html('<td colspan="10" width="100%">'+get_question_box(store_id, rel[0], rel[1])+'</td>');
      $parent.after($container);
      $container.find('.question-container').slideDown("fast");
    }      
      
  });

  $('.answer').live('click', function(){
    var rel = $(this).attr('rel');
    rel = rel.split(':|:');
    $parent = $(this).parent().parent();
    $container = $parent.next();
    if($container && $container.attr('class') == 'answer-box') {
      $container.slideUp("fast");
      $container.remove();
    } else {
      $container = $('<tr class="answer-box"></tr>');
      $container.html('<td colspan="6" width="100%">'+get_answer_box(store_id,rel[0],rel[1],rel[2])+'</td>');
      $parent.after($container);
      $container.find('.answer-container').slideDown("fast");
    }
      
    $('#reference_type').val(rel[0]);
    $('#reference_id').val(rel[1]);
    $('#post_id').val();
  });
});

function get_product_by_catagory(url, cat_id, element, type)
{
  make_row_selected(element);

  if(type == 'category')
  {
    data = {
      'cat_id': cat_id
    };
  }
  if(type == 'brand')
  {
    data = {
      'brand_id':cat_id
    };
  }
  product = 'product';
  $.post(url, data, function(data){
    $('#product').html(data);
  });
    
}

function product_selected()
{
  var count = 0;
  $.each($('input[name=products[]]'), function(index, element) {
    if($(this).attr('checked')) {
      count++;
    }
  });

  return count;
}

function make_row_selected(element)
{
  $container = $(element).parent().parent();
  $container.parent().find('tr').removeClass('selected');
  $container.addClass('selected');
}

function get_answer_box(id, ref_type, ref_id,parent_id,post_id)
{
  if(!post_id)
    post_id = 0;
  return '<iframe src="'+base_url+'post/addQuestion/'+id+'/answer'+'/'+ref_type+'/'+ref_id+'/'+parent_id+'/'+post_id+'" id = "frame_'+ref_type+'_'+ref_id+'" width="100%" height="500" scrolling="no" frameborder="0">\
            <p>Your browser does not support iframes.</p>\
            </iframe>';
}
  
function get_question_box(id, ref_type, ref_id,post_id)
{  
  if(!post_id)
    post_id = ''
  return '<iframe src="'+base_url+'post/addQuestion/'+id+'/question'+'/'+ref_type+'/'+ref_id+'/0/'+post_id+'" id = "frame_'+ref_type+'_'+ref_id+'" width="100%" height="500" scrolling="no" frameborder="0">\
            <p>Your browser does not support iframes.</p>\
            </iframe>';
}

function related_products(post, type) {
  var html = '';
  if(typeof post.related != 'undefined' && post.related.length > 0) {
    html = '<p class="related-prod-head"><strong>Products from my '+type+'</strong></p>';
    for(var i = 0; i < post.related.length; i++) {
      var tx = post.related[i].title;
      if(post.related[i].url) tx = '<a href="'+post.related[i].url+'" target="_blank">'+tx+'</a>';
      if(post.related[i].image) tx = '<span class="left"><img align="top" src="'+post.related[i].image+'" /></span><span class="img-cont">' + tx + '</span>';
      html += '<p class="related-prod">'+tx+'</p>';
    }
  }

  return html;
}

function post_details(post)
{
  var html = "<td><strong>Q:</strong> " + post.qa_title  + " <a href='javascript:;' class='view-details'>(view details)</a>" ;
  html += "<div class='post-details' style='display:none'><table cellpadding='0' cellspacing='0'><tr valign='top'>";
  if(post.image_url != null)
    html += "<td style='padding-right:5px'><img src='"+base_url+"uploads/stores/"+store_id+"/t-"+post.image_url +"'height='100px' width='100px' /></td>";

  html += "<td>" + post.qa_description + "</td></tr></table>"
  html += related_products(post, 'question');
  html += load_video(post);

  html += "</div></td>";

  return html;
}

function viewQuestion(id, type, element, offset)
{
  if(!offset)
    offset = 0;

  make_row_selected(element);
  $.ajax({
    type: "GET",
    url: base_url+"post/displayQuestion/"+store_id+"/"+id+"/"+type+"/"+offset,
    dataType: "json",
    success: function(data){
      data = eval(data);
      data = data[0];
      var pager = data.pager;
      var count = data.count;
      var role = data.role;
      var permission = data.permission;
      data = data.results;
      var html = "<div class='header_rgt'><h1>Questions</h1></div>\
          <p style='padding-top:5px'><a href='javascript:;' class='expand-list'>Expand All</a></p>\
          <table cellpadding='0' cellspacing='0' width='100%' class='stores-list' border='1px'>\
            <tr><th><strong>Question</strong></th>"+(permission == 'both' ? '</strong><th><strong>Add Answer</strong></th>' : '')+"<th><strong>View Answer</strong><th><strong>Action</th></th></tr>";
         
      if(data && data.length) {
        for(var i=0; i < data.length; i++)
        {
          html += "<tr valign='top'>";
          html += post_details(data[i]);
          
          if(permission == 'both')
            html += "<td align='center'><a href = 'javascript:;' rel= '"+type+":|:"+id+":|:"+data[i].qa_post_id +"'class='answer'>Add Answer</a></td>" ;
          
          html += "<td align='center'><a href = 'javascript:;' rel= '"+type+":|:"+id+":|:"+data[i].qa_post_id +"'class='viewAnswer' id ='viewAnswer' onclick = viewAnswer(this)>View Answer</a></td>" ;
          if(role !='view')
            html += "<td align='center'><a href = 'javascript:;' rel= '"+type+":|:"+id+":|:"+data[i].qa_post_id +"'class='delete'>Delete</a>/<a href = 'javascript:;' rel= '"+type+":|:"+id+":|:"+data[i].qa_post_id+":|:question" +"'class='editPost'>Edit</a></td>" ;
          else
            html += "<td align='center'>-/-</td>";
          html += "</tr>";
        }
        if(count >10)
        {
          html+="<tr align='center'><td colspan='5'>";
          html+= pager;
          html+="</td></tr>";
        }
      } else {
        html += "<tr><td align='center' colspan='4'>No questions posted for this "+type+" yet!</td></tr>" ;
      }
      html += "</table>";
      $('#viewQuestion').html(html);
      $('#answer').hide();
    }
  });
}

function save_product_group(element, type)
{
  if(!product_selected()) {
    $('#group-error').html('Select at least one product').css('display', 'inline-block');
    return false;
  }

  if($(element).val() == 'new_group' && !type) {
    $('#groupInfo').slideDown();
    return false;
  }

  if($(element).val() == 'new_group' && $('#group_name').val().replace(/\s+/, '') == '') {
    $('#group-error').html('Please enter the group name').css('display', 'inline-block');
    return false;
  }

  $('#group-error').hide().html('');
  html = '';
  var post_data = $('#productsFrm').serialize() + '&' + $('#groupFrm').serialize();
  $.post(base_url+"post/makeGroup", post_data, function(data) {
    $('#listGrp').slideUp();
    append_new_group(data, $('#group_name').val());
    $('#group_name').val('');
    showNewGroup();
      
  });
}
function showNewGroup()
{
  $.post(base_url+'post/getGroups', function(data){
    data = eval(data);
    html+='<table cellpadding="0" cellspacing="0" width="100%" class="stores-list" border="1px">\
           <thead>\
            <tr>\
              <th><strong>Group Id</strong></th>\
              <th><strong>Group Name</strong></th>\
              <th><strong>View</strong></th>\
              <th><strong>Delete</strong></th>\
            </tr>\
          </thead>';
    for(i = 0; i<data.length;i++)
    {
      html+='<tr>';
      html+='<td>'
      html+=data[i].qa_group_id;
      html+='</td>';
      html+='<td>';
      html+=data[i].qa_name;
      html+='</td>';
      html+='<td>';
      html+='<a href="javascript:;"class="viewGrp" rel="'+data[i].qa_group_id+'">View</a>';
      html+='</td>';
      html+='<td>';
      html+='<a href="javascript:;" class="deleteGrp" rel="'+data[i].qa_group_id+'">Delete</a>';
      html+='</td>';
      html+='</tr>';
    }
    html+='</table>';
    $('#groups').html(html);
    $('#groups').slideDown('fast');
  });
}

function append_new_group(group_id, group_name)
{
  var add = true;
  $.each($('#list_group').find('option'), function(index, element){
    if($(element).attr('value') == group_id)
      add = false;
  });

  if(add) {
    $('#list_group').append('<option value="'+group_id+'">'+group_name+'</option>');
  }
}

function getMoreProducts(url)
{
  product = 'product'
  html='';
  $.get(url,function(data){        
    $('#product').html(data);
  });
}

function getMoreCategories(url)
{
  html='';
  $.get(url,function(data){         
    $('#category').html(data);
  });
}

function getMoreBrands(url)
{
  html='';
  $.get(url,function(data)
  {    
    $('#brand').html(data);
  });
}
  
function viewAnswer(element, ref_type, ref_id, parent_id, offset)
{
  $('#answer').show();
  if(!offset)
    offset = 0;
  
  var rel = $(element).attr('rel');
  if(rel)
    rel = rel.split(':|:');
  else
  {
    rel = [ref_type, ref_id, parent_id];
  }
  make_row_selected(element);
  $.ajax({
    type: "GET",
    url: base_url+"post/displayAnswer/"+store_id+"/"+rel[0]+"/"+rel[1]+"/"+rel[2]+"/"+offset,
    dataType: "json",
    success: function(data){
      eval(data);
      data = data[0];
      type = "answer";
      var pager = data.pager;
      var count = data.count;
      var role = data.role;
      data = data.results;
      
      var html = "<div class='header_rgt'><h1>Answers</h1></div>\
        <p style='padding-top:5px'><a href='javascript:;' class='expand-list'>Expand All</a></p>\
        <table cellpadding='0' cellspacing='0' width='100%' class='stores-list' border='1px'>\
          <tr><th><strong>Answer</strong></th><th><strong>Action</strong></th></tr>";

      if(data && data.length) {
        for(var i=0; i < data.length; i++)
        {
          html += "<tr>";
          html += post_details(data[i]);
          if(role!='view')
            html += "<td align='center'><a href = 'javascript:;' rel= '"+type+":|:"+store_id+":|:"+data[i].qa_post_id +"'class='delete'>Delete</a>/<a href = 'javascript:;' rel= '"+rel[0]+":|:"+rel[1]+":|:"+data[i].qa_post_id+":|:answer"+":|:"+rel[2] +"'class='editPost'>Edit</a></td>" ;
          else
            html+="<td align='center'>-/-</td>"
          html += "</tr>";
        }
        if(count > 10)
        {
          html+="<tr align='center'><td>";
          html+= pager;
          html+="</td></tr>";
        }
      } else {
        html += "<tr><td align='center'>No Answer has posted for this "+$('#reference_type').val()+" yet!</td></tr>" ;
      }
      html += "</table>";
      $('#answer').html(html);
    }
  });
    
}

function load_video(post) {
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
      return '<div class="video">\
          <p class="video-head"><strong>Video related to my answer</strong></p>\
          '+(post.video_caption ? '<p>'+post.video_caption+'</p>' : '')+'\
          <p><iframe title="'+(post.video_caption ? post.video_caption : '')+'" width="240" height="210" src="http://www.youtube.com/embed/'+video_id+'" frameborder="0" allowfullscreen="false"></iframe></p>\
        </div>';
    }
  }

  return '';
}

function remove_frame(element_id)
{
  $('#'+element_id).parent().parent().remove();
    
}
function edit_category(id,type,element)
{
  $parent = $(element).parent().parent();
  $container = $parent.next();
  if($container && $container.attr('class') == 'edit-container') {
    $container.slideUp("fast");
    $container.remove();
  }else {
    $container = $('<tr class="edit-container"></tr>');
    $container.html('<td colspan="10" width="100%">'+get_edit_box(store_id,id, type)+'</td>');
    $parent.after($container);
    $container.find('.edit-container').slideDown("fast");
  }
}
function get_edit_box(store_id,id,type)
{
  var html = '<form action=""  method="post" id ="editForm_'+id+ '" class="constrain">';
  if(type == 'category')
  {
    html +=  '<div><label>Category Id </label><input type = "text" class = "required" id = "cid_'+id+'" name = "id" value = ""/></div>';
    html +=  '<div><label>Category Name</label> <input type = "text" class = "required" id = "cname_'+id+'" name = "name" value = ""/></div>';
  }
  else
  {
    html +=  '<div><label>Bramd Id </label><input type = "text"  class = "required"  id = "bid_'+id+'" name = "id" value = ""/></div>';
    html +=  '<div><label>Brand Name</label> <input type = "text"  class = "required"  id = "bname_'+id+'" name = "name" value = ""/></div>';
  }
  html += '<div><input type = "button" value ="Edit" class = "edit" rel ="'+type+':|:'+id+'"/></div></form>';
  $.post(base_url+'post/editCategory/'+store_id+'/'+id+'/'+type,function(data){
    data = eval(data);
    if(type == "category"){
      $('#cid_'+id).val(data[0].qa_category_id);
      $('#cname_'+id).val(data[0].qa_category_name);
    }
    else{
      $('#bid_'+id).val(data[0].qa_brand_id);
      $('#bname_'+id).val(data[0].qa_brand_name);
    }
  });
  return html;
}
function edit_product(id,element)
{
  $parent = $(element).parent().parent();
  $container = $parent.next();
  if($container && $container.attr('class') == 'edit-container')
  {
    $container.slideUp("fast");
    $container.remove();
  }else {
    $container = $('<tr class="edit-container"></tr>');
    $container.html('<td colspan="10" width="100%">'+get_product_box(store_id,id)+'</td>');
    $parent.after($container);
    $container.find('.edit-container').slideDown("fast");
  }
}
function get_product_box(store_id,id)
{
  return '<iframe src="'+base_url+'post/editProduct/'+store_id+'/'+id+'" id = "frame_'+id+'" width="100%" height="300" scrolling="no" frameborder="0">\
            <p>Your browser does not support iframes.</p>\
            </iframe>';
}

function validate_form (form)
{
  var valid = true;
  var regExp = new RegExp("\\w+([-+.\']\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*");
  $.each($(form).find('.required'), function(index, element){
    var is_empty = ($(element).val().replace(/\s+/, '') == '');
    var error_msg = 'This field is required';
    var invalid_email = false;
    if(!is_empty && $(element).hasClass('email') && !regExp.test($(element).val())) {
      error_msg = 'Email is not valid';
      invalid_email = true;
    }
    if(is_empty || invalid_email) {
      if(!$(element).next().hasClass('error')) {
        $(element).after('<span class="error">'+error_msg+'</span>');
      } else {
        $(element).next().text(error_msg);
      }
      valid = false;
    } else if($(element).next().hasClass('error')) {
      $(element).next().remove();
    }
  });

  return valid;
}

function confirmation(id,type)
{
  var res = confirm('Are you sure, you want to delete this '+type +'?');
  if(res) {
    $.post(base_url+'post/deleteCategory/'+store_id+'/'+id+'/'+type,function(){
      window.location.reload();
    });
  }
}


