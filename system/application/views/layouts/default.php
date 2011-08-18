<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type" />
    <title></title>
    <?php echo link_tag('css/styles.css'); ?>
    <?php echo link_tag('css/validationEngine.jquery.css'); ?>		
    <?php echo link_tag('css/vtip.css'); ?>	

    <script type="text/javascript">
      var base_url = "<?php echo base_url() ?>";
      var script_name = "index.php/";
      var site_url = "<?php echo site_url() ?>";
    </script>

    <script type="text/javascript" src="<?php echo base_url() ?>js/safeEnter.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/main.js"></script>

    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery.validate.min.js"></script>
    <script src="<?php echo javascriptUrl(); ?>jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo javascriptUrl(); ?>jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo javascriptUrl(); ?>validation.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?php echo javascriptUrl(); ?>vtip.js" type="text/javascript" charset="utf-8"></script>
    <?php echo include_javascript();?>
  </head>

  <body>

    <?php $invite_count = getInvites($this->uid); ?>

    <div id="header">	
      <div class="left">
        <h1>Q&A Wiki&nbsp;<sub style="color:red;">BETA</sub></h1>
      </div>
      <div class="right">
        <?php if ($this->session->userdata('uid')): ?>
          Welcome <?php echo $this->session->userdata('name'); ?>!&nbsp;&nbsp;
          <a href="<?php echo base_url() ?>dashboard">My Stores</a>
          &nbsp;|&nbsp;
          <a href="<?php echo base_url() ?>teams">My Teams</a>
          &nbsp;|&nbsp;
          <a href="<?php echo base_url() ?>reports">Reports</a>
          &nbsp;|&nbsp;
          <a href="<?php echo base_url() ?>moderator/index/">Moderate</a>
          &nbsp;|&nbsp;
          <a href="<?php echo base_url() ?>account">My Account</a>
          &nbsp;|&nbsp;
          <?php if($this->session->userdata('is_admin') == 1): ?>
            <a href="<?php echo base_url() ?>admin">Admin Panel</a>
            &nbsp;|&nbsp;
          <?php endif;?>

        <?php if ($invite_count): ?>
            <a href="<?php echo base_url() ?>teams/invites">My Invites(<?php echo $invite_count ?>)</a>
            &nbsp;|&nbsp;
        <?php endif; ?>
            <a href="<?php echo base_url() ?>register/logout">Logout</a>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
      <hr/>
    </div>
    <div class="clear"></div>
    <div id="main">
      {yield}      
    </div>
    <div id="footer">
      <center>
        <hr/>
	      Copyright: 2011
      </center>
    </div>
  </body>
</html>