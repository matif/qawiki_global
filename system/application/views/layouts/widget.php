<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Widget</title>
    <meta content="text/html; charset=utf-8" http-equiv="content-type" />
    
    <?php echo link_tag('css/widget/widget.css'); ?>

  	<script type="text/javascript" src="<?php echo base_url()?>js/jquery-1.4.2.min.js"></script>

  </head>

  <body>

    <div id="qawiki-widget">
      {yield}
    </div>

  </body>
</html>