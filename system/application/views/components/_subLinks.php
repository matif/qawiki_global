
<?php $row = isset($this->store_slot['store']) ? (array)$this->store_slot['store'] : array();?>

<div class="main_nav_area clearfix">
  
  <div class="store_name">
    <?php echo isset($this->store_slot['heading']) ? $this->store_slot['heading'] : $this->store_slot['store']->qa_store_name?>
  </div>
  
  <ul class="main_nav">
    
    <?php if(isset($this->store_slot['sub_links'])) :?>
    
      <?php foreach($this->store_slot['sub_links'] as $sub_link) :?>

        <li <?php echo ($this->store_slot['selected'] == $sub_link['selected']) ? 'class="selected"' : ''?>>

          <a href="<?php echo parse_dynamic_vars($sub_link['url'], $row)?>" class="<?php echo $sub_link['class']?>"><?php echo $sub_link['text']?></a>

        </li>

      <?php endforeach;?>
    
    <?php endif;?>
    
  </ul>
  
</div>

<div class="sub_link_panel">
  <ul>
    
    <?php if(isset($this->store_slot['inner_links'])):?>

      <?php foreach($this->store_slot['inner_links'] as $inner_link):?>
    
        <?php $drp = top_nav_drop_down($inner_link['text'], (isset($this->store_slot['drop_down']) ? $this->store_slot['drop_down'] : ''));?>
    
        <li class="<?php echo ($this->store_slot['inner_selected'] == $inner_link['text']) ? 'export' : ''?> <?php echo trim($drp) ? 'tooltip' : ''?>">
          
          <a href="<?php echo parse_dynamic_vars($inner_link['url'], $row)?>"><?php echo $inner_link['text']?></a>
          
          <?php echo parse_dynamic_vars($drp, $row);?>
          
        </li>
    
      <?php endforeach;?>
  
    <?php elseif(isset($this->store_slot['sub_heading'])):?>
  
      <li class="export"><?php echo $this->store_slot['sub_heading']?></li>
  
    <?php endif;?>
  
  </ul>
</div>