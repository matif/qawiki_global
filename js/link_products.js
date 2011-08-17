
$(document).ready(function(){
  $('.link-products').bind('click', function(){
    search_popup();
    return false;
  });

  $('.qa-popup-close').live('click', function(){
    $('#qawiki-window').remove();
    $('#qawiki-overlay').remove();
  });

  $('.qawiki-cat').live('click', function(){
    $('.qawiki-cat').removeClass('qawiki-cat-selected');
    $(this).addClass('qawiki-cat-selected');
    var $container = $('.link-products');
    var params = get_linked_products();
    $.post(base_url + 'link_products/products/' + store_id + '/' + $(this).attr('rel'), params, function(response){
      response = eval(response);
      $('#qawiki-products').html(response);
    });
    
    return false;
  });

  $('.qawiki-add-product').live('click', function(){
    show_linked_product(this);
    return false;
  });

  $('.remove').live('click', function(){
    var $parent = $(this).parent();
    $parent.parent().find('.link-products', 0).show();
    $parent.remove();
    pord_cnt--;
  });
});

function show_linked_product(element) {
  $container = $('.add-related-link');  
  if(pord_cnt == 3) {
    return false;
  }
  if($container.length > 0) {
    $(element).removeClass('qawiki-add-product').addClass('qawiki-product-added');
    $(element).find('a', 0).text('Added');
    var content = $(element).prev().text();
    $container.before(linked_product(content, $(element).attr('rel')));
    if(parseInt(pord_cnt) + 1 == 3) {
      $container.hide();
    }
    pord_cnt++;
  }
}

function linked_product (title, product_id) {
  return '<div class="link-products">\
    <div class="remove">REMOVE</div>\
    <div class="linked-prod"><table cellpadding="0" cellspacing="0"><tr><td>'+title+'</td></tr></table></div>\
    <input type="hidden" name="products[]" value="'+product_id+'" />\
  </div>';
}

function wiki_popup(popup_contents, p_width) {
  if(!p_width) p_width = 300;
  var scrollTop = $('body').scrollTop();
  var topPos = (!is_iframe_version) ? scrollTop + ((page_height() - 100) / 2) : 25;
  var leftPos = ($('body').width() - p_width) / 2;
  $('body').append('<div class="qawiki-overlay-bg" id="qawiki-overlay"></div>\
    <div id="qawiki-window" style="top: '+topPos+'; left: '+leftPos+'; width: '+p_width+'px; display: block;">\
      <div>\
        '+popup_contents+'\
        <span class="qa-popup-close"><a href="javascript:;">close</a></span>\
      </div>\
    </div>'
  );
}

function page_height() {
  return window.innerHeight != null ?
    window.innerHeight
    : document.documentElement && document.documentElement.clientHeight ?
        document.documentElement.clientHeight
        : document.body != null ? document.body.clientHeight
      : null;
}

function search_popup()
{
    $.getJSON(base_url + 'ajax/search/' + store_id, function(response){
      qawiki_popup(response, 500);
    });
    return false;
}
function qawiki_popup(popup_contents, p_width) {
    if(!p_width) p_width = 300;
    var scrollTop = $('body').scrollTop();
    var topPos = scrollTop + ((page_height() - 100) / 2);
    var leftPos = ($('body').width() - p_width) / 2;
    topPos = Math.round(topPos.toString().replace('px', ''));
    leftPos = Math.round(leftPos.toString().replace('px', ''));
    $('#search').append('<div class="qawiki-overlay-bg" id="qawiki-overlay"></div>\
      <div id="qawiki-window" style="top: '+topPos+'px; left: '+leftPos+'px; width: '+p_width+'px; display: block;">\
        <div id="qawiki-overlay-container">\
          '+popup_contents+'\
          <span class="qa-popup-close"><a href="javascript:;">close</a></span>\
        </div>\
      </div>'
    );
  }

  function browse_products() {    
    $.getJSON(base_url + 'ajax/categories/' +store_id, function(response){
      $('#qawiki-popup-content').html(response);
      $('#qaPopup_Desc').text('Navigate through the categories to find a product.');      
    });
  }

  function search_product() {
    var search_key = $('#qawiki_psearch').val();
    if(search_key.replace(/\s+/, '') != ''){
      //var params = get_linked_products();
      var data = "search="+search_key+'&'+get_linked_products();

      $.post(this.base_url + 'ajax/search_products/' + this.store_id , data, function(response){
        if(response != 'failure'){
          $('#qawiki-popup-content').html(response);
          $('#qaPopup_Desc').html('Products that match the term <strong>"'+search_key+'"</strong>');

        }

      });
    }
    return false;
  }

  function get_linked_products(){
    var $container = $('#qawiki-link-prod');
    var params = 'qawiki_products=';
    $.each($container.parent().find('input[name=qawiki_products[]]'), function(ind, ele){
      params += $(ele).val()+',';
    });    
    return params;
  }
