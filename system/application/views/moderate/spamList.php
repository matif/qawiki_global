<?php echo grid_libraries()?>
<div>
  <?php echo grid_title_html($title, 'close'); ?>
  <table id="<?php echo $title?>_<?php echo $type?>_list"></table>
  <div id="<?php echo $title?>_<?php echo $type?>_pager"></div>

  <?php
   echo render_grid($title.'_'.$type.'_list', $url, array(
      'caption'         => '',
      'record_per_page' => $grid_limit,
      'pager'           => '#'.$type.'_pager',
      'sort_column'     => $grid_column,
      'sort_order'      => $grid_order,
      'head'            => array('Id', $type.' Id', $type.' name', 'Update','Spam History'),
      'columns'         =>  array(array(
      'name'   => 'Id',
      'index'  => 'id',
      'width'  => 300
      ), array(
        'name'  => $type.' Id',
        'index' => 'qa_'.$type.'_id',
        'width' => 300
      ), array(
        'name'  => $type.' Name',
        'index' => 'qa_'.$type.'_name',
        'width' => 300
      ), array(
        'name'     => 'Update',
        'sortable' => false,
        'width'    => 125
      ),array(
        'name'     => 'Spam History',
        'sortable' => false,
        'width'    => 115
      )
    ))
   );
  ?>
</div>