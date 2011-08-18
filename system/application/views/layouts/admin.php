
<html>
  <head>
    <title>Q&A Wiki - Admin Panel</title>

    <?php echo link_tag('css/admin.css'); ?>

    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/admin/main.js"></script>
    <script type="text/javascript">
      var base_url = '<?php echo base_url()?>';
    </script>
  </head>

  <body>

    <div id="main">

      <div id="header"><a href="<?php echo base_url()?>">Q&A Wiki</a></div>

      <div id="sub-nav">
        <ul>
          <li class="first"><a href="<?php echo base_url().'admin'?>">Email Templates</a></li>
          <li><a href="<?php echo base_url().'admin/staff'?>">Staff</a></li>
        </ul>
      </div>
      <div class="clear"></div>

      <div id="content">

        <div class="content-left">
          <?php if(isset($this->left_nav)):?>

          <?php endif;?>
        </div>

        <div class="content-right">
          <div class="inner-content">
            {yield}
          </div>
        </div>

        <div class="clear"></div>
      </div>

    </div>

  </body>

</html>