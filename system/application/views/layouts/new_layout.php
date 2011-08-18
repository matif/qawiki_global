<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Q&amp;A Wiki</title>
    
    <?php echo link_tag('css/qawiki.css'); ?>
    <?php echo link_tag('css/content.css'); ?>    
    
    
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/ajaxfileupload.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/safeEnter.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>js/custom-form-elements.js"></script>    
    <script type="text/javascript" src="<?php echo base_url() ?>js/jquery.validate.min.js"></script>
  
    <script type="text/javascript" src="<?php echo base_url() ?>js/main.js"></script>
    
    <?php echo include_javascript();?>
    
    <?php $main_store_config = store_list_redirect_url()?>
    
    <script type="text/javascript">
      var base_url = "<?php echo base_url() ?>";
      var store_redirect_url = '<?php echo $main_store_config[0]?>';
      var store_id = '<?php echo (isset($this->store_id)) ? $this->store_id : (isset($store_id) ? $store_id : '') ?>';
    </script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo base_url()?>css/jquery/jquery-ui.custom.css" />
    
  </head>
  
  <body class="<?php echo isset($this->body_class) ? $this->body_class : ''?>">
    
    <div style="display: none" id="quick_message" class="quick_message processing">
      <h2><div></div><span>Please wait...</span></h2>
    </div>
    
    <!--start of main container-->
    <div class="outer_wraper">
      <?php $invite_count = getInvites($this->uid); ?>
      <div class="header_container">
        <div class="logo_area clearfix">
          <div class="logo"><a href="<?php echo base_url()?>"><img src="/images/frontend/logo.png" alt="Sticky Q&A" title="Sticky Q&A"/></a></div>
          <div class="rgt_nav clearfix ">
            <div class="top_nav">
              <div class="lft_nav">
                <ul class="tp_nav">
                  <?php if ($invite_count): ?>
                      <li><a href="<?php echo base_url() ?>teams/invites">My Invites(<?php echo $invite_count ?>)</a></li>
                  <?php endif; ?>
                  <li><a href="<?php echo base_url() ?>account">My Account</a></li>                                    
                  <li><a href="#">Help</a></li>
                  <li><a href="<?php echo base_url() ?>register/logout">Logout</a></li>
                </ul>
              </div>
            </div>
            <div class="clear"></div>
            <div class="bookmark_area clearfix">
              <!--<div class="bookmark"><a href="#">Bookmark<br />This Page</a></div>-->
              <div class="active clearfix">
                <?php if(getStoreCount()!= 0):?>
                  <div class="tag_active">Active:</div>                 
                  <div class="select_box">                                                 
                    <?php echo select_tag('main_store_id', $this->stores_list, $main_store_config[1], array('class' => 'styled'),array(),array(),"on")?>                  
                  </div>
                <?php endif?>
                
              </div>
              <div class="clear"> </div>
            </div>
          </div>
          <?php /*<div class="rgt_nav clearfix ">
            <div class="top_nav">
              <div class="lft_nav">
                <ul class="tp_nav">
                
                  <?php if ($this->session->userdata('uid')): ?>
                  
                    
                    
                    <li><a href="<?php echo base_url() ?>account">My Account</a></li>
                    <li><a href="<?php echo base_url() ?>teams">My Teams</a></li>
                    <li><a href="<?php echo base_url() ?>reports">Reports</a></li>
                    <li><a href="<?php echo base_url() ?>moderator/index">Moderate</a></li>
                    
                    <?php if($this->session->userdata('is_admin') == 1): ?>
                      <li><a href="<?php echo base_url() ?>admin">Admin Panel</a></li>
                    <?php endif;?>

                    <?php if ($invite_count): ?>
                      <li><a href="<?php echo base_url() ?>teams/invites">My Invites(<?php echo $invite_count ?>)</a></li>
                    <?php endif; ?>
                      
                    <li><a href="<?php echo base_url() ?>register/logout">Logout</a></li>
                  <?php endif; ?>
                  
                </ul>
              </div>
            </div>
            <div class="clear"></div>
          </div>*/?>
        </div>
        
        <div class="select_store_panel">
          <ul>
            <li><a href="<?php echo base_url() ?>">Q&amp;A Home</a></li>
            <?php if(getStoreCount()!= 0):?>
            <li>Go to Q&amp;A: 
              <?php echo select_tag('main_store_id', $this->stores_list, $main_store_config[1], array('class' => 'styled'),array(),array(),"on")?>
            </li>
            <?php endif;?>
          </ul>
        </div>
        
        <?php if(isset($this->store_slot)):?>
          
          <?php echo $this->load->view('components/_subLinks', array(), true);?>
        
        <?php endif;?>
        
        
      </div>
      
      <!--start of main container-->
      <div class="content_panel">
        {yield}
      </div>
      
    </div>
    
    <div class="footer">
      <div class="footer_container">
        <div id="show_hide"><a href="#">Show</a></div>
        <div id="footer_show" class="clearfix">
          <div class="footer_show_panels">
            <h1>News</h1>
            <div class="content_are_footer">
              <ul class="question_answer">
                <li class="question">Need help getting your store setup?</li>
                <li class="ans"><a href="#">Click here to get started!</a></li>
                <li class="question">Sticky Apps just launched Q&A</li>
                <li class="ans"><a href="#">Click here to learn more!</a></li>
              </ul>
            </div>
          </div>
          <div class="footer_show_panels small_panels">
            <h1>Alerts</h1>
            <div class="content_are_footer">
              <div class="section clearfix">
                <h4>Today</h4>
                <div class="content_alert">You have <strong>10 new items in the buy box</strong></div>
                <div class="action_alert"><a href="#"><img src="/images/frontend/tic.png" alt="Tic" title="Tic" width="10" height="10" /></a><a href="#"><img src="/images/frontend/close.png" alt="Close" title="Close" /></a></div>
                <div class="clear"></div>
                <div class="content_alert">You have <strong>3 less featured products</strong></div>
                <div class="action_alert"><a href="#"><img src="/images/frontend/tic.png" alt="Tic" title="Tic" width="10" height="10" /></a><a href="#"><img src="/images/frontend/close.png" alt="Close" title="Close" /></a></div>
              </div>
              <div class="section clearfix">
                <h4>April 12, 2011</h4>
                <div class="content_alert">You have <strong>1 new items in the buy box</strong></div>
                <div class="action_alert"><a href="#"><img src="/images/frontend/tic.png" alt="Tic" title="Tic" width="10" height="10" /></a><a href="#"><img src="/images/frontend/close.png" alt="Close" title="Close" /></a></div>
                <div class="clear"></div>
                <div class="content_alert">You have <strong>2 less featured products</strong></div>
                <div class="action_alert"><a href="#"><img src="/images/frontend/tic.png" alt="Tic" title="Tic" width="10" height="10" /></a><a href="#"><img src="/images/frontend/close.png" alt="Close" title="Close" /></a></div>
              </div>
            </div>
          </div>
          <div class="footer_show_panels small_panels">
            <h1>Reports</h1>
            <div class="content_are_footer">
              <ul class="reports">
                <li><a href="#">notProcessed_4-12-2011</a></li>
                <li><a href="#">notProcessed_4-12-2011</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="footer_visible">
          <p class="messages">You have <strong>2 unread</strong> items.</p>
          <ul class="footer_links">
            
            <li><a href="<?php echo base_url() ?>">Q&amp;A Wiki Home</a></li>
            
            <?php if($this->session->userdata('uid')): ?>
            
              <li><a href="<?php echo base_url() ?>account">Account</a></li>
              <li><a href="<?php echo base_url() ?>register/logout">Logout</a></li>
              
            <?php endif; ?>
              
          </ul>
          <div class="clear"></div>
          <p><a href="#">Terms and Conditions</a> <a href="#">Privacy Policy</a> All Rights Reserved. &copy; 2011 <a href="#">StickySolutions</a></p>
        </div>
      </div>
    </div>
  </body>
</html>
