<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Q&amp;A Wiki</title>
    
    <?php echo link_tag('css/qawiki.css'); ?>
    <?php echo link_tag('css/content.css'); ?>
    
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/safeEnter.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/custom-form-elements.js"></script>    
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery.validate.min.js"></script>
  
    <script type="text/javascript" src="<?php echo base_url() ?>js/main.js"></script>
    
    <?php echo include_javascript();?>
    
    <?php $main_store_config = store_list_redirect_url()?>
    
    <script type="text/javascript">
      var base_url = "<?php echo base_url() ?>";
    </script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo base_url()?>css/jquery/jquery-ui.custom.css" />
    
  </head>
  
  <body class="short_header">
    
    <div style="display: none" id="quick_message" class="quick_message processing">
      <h2><div></div><span>Please wait...</span></h2>
    </div>
    
    <!--start of main container-->
    <div class="outer_wraper">

      <div class="header_container">
        <div class="logo_area clearfix">
          <div class="logo"><a href="<?php echo base_url()?>"><img src="/images/frontend/logo.png" alt="Sticky Q&A" title="Sticky Q&A"/></a></div>
          <div class="rgt_nav clearfix ">
            <div class="top_nav">
              <div class="lft_nav">
                <ul class="tp_nav">
                  <?php if(in_array($this->current_action, array('forgotpassword', 'signup'))):?>
                    <li><a href="<?php echo base_url() ?>register">Sign In</a></li>
                  <?php endif;?>
                  
                  <?php if($this->current_action != 'signup'):?>
                    <li><a href="<?php echo base_url() ?>register/signUp">Sign Up</a></li>
                  <?php endif;?>
                  
                  <?php if($this->current_action != 'forgotpassword'):?>
                    <li><a href="<?php echo base_url() ?>register/forgotpassword">Forgot your password?</a></li>
                  <?php endif;?>
                </ul>
              </div>
            </div>
            <div class="clear"></div>
          </div>
        </div>
        
        <div class="select_store_panel">
          
        </div>       
        
      </div>
      
      <!--start of main container-->
      <div class="content_panel">
        {yield}
      </div>
      
    </div>
    
    <div class="footer" style="background-position:0 -42px; padding-top:15px;">
      <div class="footer_container" style="min-height:40px;">
        <p><a href="#">Terms and Conditions</a> <a href="#">Privacy Policy</a> All Rights Reserved. &copy; 2011 <a href="#">StickySolutions</a></p>
      </div>
    </div>
  </body>
</html>
