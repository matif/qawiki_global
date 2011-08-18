
<h2><?php echo $title?></h2>

<div>
  <div class="move-items mv-left">

    <ul>
      <?php foreach($items as $item):?>

        <li rel="<?php echo $item['id']?>">
          <?php echo $item[$item_field]?>
        </li>

      <?php endforeach;?>
    </ul>

    
    <?php if(isset($picker) && trim($picker)):?>
    
      <div class="item-picker">
        <input type="button" value="<?php echo $picker?>" rel="<?php echo $picker_route?>" class="item-picker-btn" />
      </div>
    
    <?php endif;?>
    
  </div>

  <div class="move-actions">
    <input type="button" value="ADD >>" rel="add" /><br/>
    <input type="button" value="<< REMOVE" rel="remove" />
  </div>

  <div class="move-items" <?php echo isset($move_id) ? 'id="'.$move_id.'"' : ''?> rel="<?php echo $post_field?>">
    <ul></ul>
  </div>
</div>
<div class="clear"></div>

<?php if(isset($move_id)) unset($move_id);?>