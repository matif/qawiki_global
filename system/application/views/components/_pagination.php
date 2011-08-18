
<?php if(!isset($page_element_id)) $page_element_id = 'pagination';?>


<ul id="<?php echo $page_element_id?>" <?php echo ($total_records) ? '' : 'style="display:none"'?>>
  <li><a class="first" href="javascript:;" rel="<?php echo ($total_records == 0 ? 0 : 1)?>"></a></li>
  <li><a class="pre" href="javascript:;"  <?php echo ($current_page == 1) ? 'rel="1"' : 'rel="'.($current_page - 1).'"'?>></a></li>
  <li>
    <input type="text" name="page_number" value="<?php echo ($total_records == 0 ? 0 : $current_page)?>" /> / <?php echo $total_pages?>
    <input type="hidden" name="page_total" value="<?php echo $total_pages?>" />
  </li>
  <li><a class="next" href="javascript:;" <?php echo ($current_page == $total_pages) ? 'rel="'.$current_page.'"' : 'rel="'.($current_page + 1).'"'?>></a></li>
  <li><a class="last" href="javascript:;" rel="<?php echo $total_pages?>"></a></li>
  <li>
    <?php echo select_tag('rec_per_page', array('1' => 1, '5' => 5, '10' => 10, '20' => 20), $rec_per_page)?>

    <span><?php echo $total_records?> row(s)</span>
  </li>
</ul>