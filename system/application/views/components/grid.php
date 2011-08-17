<?php echo grid_libraries()?>


<table id="list2"></table>
<div id="pager2"></div>

<script type="text/javascript">
  jQuery("#list2").jqGrid({
    url:'/grid/data', 
    datatype: "json", 
    colNames:['Inv No','Date', 'Client', 'Amount','Tax','Total','Notes'], 
    colModel:[ 
      {name:'id',index:'id', width:55}, 
      {name:'invdate',index:'invdate', width:90}, 
      {name:'name',index:'name asc, invdate', width:100}, 
      {name:'amount',index:'amount', width:80, align:"right"}, 
      {name:'tax',index:'tax', width:80, align:"right"}, 
      {name:'total',index:'total', width:80,align:"right"}, 
      {name:'note',index:'note', width:150, sortable:false} 
    ], 
    rowNum:10, 
    rowList:[10,20,30], 
    pager: '#pager2', 
    sortname: 'id', 
    viewrecords: true, 
    sortorder: "desc", 
    caption:"JSON Example" 
  }); 
  
  jQuery("#list2").jqGrid('navGrid','#pager2',{edit:true,add:false,del:false});
</script>

<table id="list"></table>
<div id="pager"></div>

<?php 
  echo render_grid('list', '/grid/data', array(
    'caption'         => 'Example JSON grid',
    'record_per_page' => 10,
    'pager'           => '#pager',
    'sort_column'     => 'Inv',
    'sort_order'      => 'desc',
    'head'            => array('Inv', 'Ok'),
    'columns'         => array(array(
      'name'  => 'Inv',
      'id'    => 'Inv',
      'width' => 150
    ), array(
      'name'  => 'Ok',
      'id'    => 'Ok',
      'width' => 150
    ))
  ));  
?>