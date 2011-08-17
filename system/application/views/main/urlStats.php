
<?php
  $url = $this->config->item('shorten_url');
  
  if($type == 'store')
  {
    $url .= 'store/'.$id;
  }
  else
  {
    $url .= 'item-stats/'.$id.'/'.$type;
  }

?>

<iframe height="800px" width="100%" frameborder="0" scrolling="0" src="<?php echo $url?>" style="overflow-y: auto; overflow-x: hidden "></iframe>