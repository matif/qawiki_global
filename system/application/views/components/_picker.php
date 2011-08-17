
<div class="<?php echo isset($parent_id) && $parent_id > 0 ? 'items-sub' : 'item-picker-dlg'?>">

  <?php if(!isset($parent_id) || !trim($parent_id)):?>
  
    <h3><?php echo $title?></h3>

    <?php if(isset($expand)):?>
      <p>Click the + icon to explore a category</p>
    <?php endif;?>

    <p>Click the ADD button to select it</p>

    <hr/>

    <p>Filter <?php echo $type?> alphabetically by name</p>

    <p id="item-sort-alpha">
      <a href="javascript:;" rel="<?php echo $rel?>/none" <?php echo ($filter == 'none') ? 'class="selected"' : ''?>>None</a>

      <?php for($i = 65; $i<91; $i++):?>
        <a href="javascript:;" rel="<?php echo $rel.'/'.chr($i)?>" <?php echo ($filter == chr($i)) ? 'class="selected"' : ''?>><?php echo chr($i)?></a>
      <?php endfor;?>

    </p>

    <hr/>

    <div class="item-container-dlg">
  
  <?php endif;?>
      
      <?php foreach($items as $item):?>

        <p class="item-row">

          <?php if(isset($item['cnt']) && $item['cnt'] > 0):?>
            <span class="item-expand visible" rel="<?php echo $item['id']?>">+</span>
          <?php else:?>
            <span class="item-expand"></span>
          <?php endif;?>

          <span class="text" rel="<?php echo $item['id']?>"><?php echo $item[$item_field]?></span>
          <span class="add">ADD</span>
        </p>

      <?php endforeach;?>
      
  <?php if(!isset($parent_id) || !trim($parent_id)):?>
    
    </div>

  <?php endif;?>
    
</div>
