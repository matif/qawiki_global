
$(document).ready(function(){
  attach_autocomplete('reports/index', 'skip');

  $('.view-report').live('click', function(){
    var tokens = $(this).attr('rel').split('|');
    item_type = tokens[0];
    item_id = tokens[1];
    update_report_chart();
  });

  $("#report_graph").css({width: '100%', height: 400});

  if(typeof qaLineGraph != 'undefined') {
    line_graph = new qaLineGraph({
      container: "report_graph",
      width: '850',
      height: '400',
      data_route: base_route + '/' + item_type + '/' + item_id,
      point_callback: ""
    });
    line_graph.render();
  }

  $("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
  $("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });

  $("#start_date, #end_date").keypress(function (e) {
    e.preventDefault();
  });

  $('#graph_update').bind('click', function(){
    update_report_chart();
  });

});

function get_action_types()
{
  var action_types = '';
  $.each($('input[name=action_type[]]'), function(index, element){
    if($(element).attr('checked')) {
      action_types += $(element).val()+'|';
    }
  });

  return action_types;
}

function update_report_chart()
{
  var start_date = ($('#start_date').val() != '') ? $('#start_date').val() : 0;
  var end_date = ($('#end_date').val() != '') ? $('#end_date').val() : 0;
  var url = base_route  + '/' + item_type + '/' + item_id + '/' + start_date + '/' + end_date+'/'+get_action_types();

  update_line_chart(line_graph, url);
}

function get_products(url)
{
  get_paginated_data(url, '#product');
}

function get_categories(url)
{
  get_paginated_data(url, '#category');
}

function get_brands(url)
{
  get_paginated_data(url, '#brand');
}

function get_paginated_data(url, element)
{
  doAjax("get", url, null, null, function(response){
      $(element).html(response);
  });
  
}