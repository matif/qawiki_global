
<h2>History </h2>
<ul>
  
  <?php foreach($history as $key => $value) :?>
  
    <li><?php echo format_time($value['created_at']);?>: <?php echo $value['message']?></li>
  
  <?php endforeach;?>
</ul>