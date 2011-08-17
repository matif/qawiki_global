<?php echo use_javascript('spamPost'); ?>
<?php echo link_tag('css/jquery/jquery-ui.custom.css'); ?>

<script type="text/javascript">
  var store_id = <?php echo $this->store_id ?>;
  var moderation_type = <?php echo $this->moderation_type ?>;
</script>

<div class="rgt_850">

  <div class="header_rgt">
    <strong>Search Stores</strong>
    <input type="text" id="searchbox" value="Type Your text Here" onclick="this.value=''" onblur="(this.value == ''? this.value ='Type Your text Here':'')" /> <span style="display: none" id="no_record">No Record Found</span>
    <div class="clear"></div>
  </div>
  <?php if($this->store_id != 0): ?>

    <div id="moderate">
      <div class="items tab-selected" id="category_select" ><strong><a href="javascript:;" onclick="">Category</a></strong></div>
      <div class="items" id="brand_select" ><strong><a href="javascript:;" onclick="">Brand</a></strong> </div>
      <div class="items" id="product_select"><strong><a href="javascript:;" onclick="">Products</a></strong></div>
      <div class="clear"></div>
    </div>

    <div class="moderate-tab-content">

      <div id="category">
        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/category_moderator/'.$this->store_id.'/spam', 'title' => 'Question', 'type' => 'category'), true);?>

        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/category_moderator/'.$this->store_id.'/spam/1', 'title' => 'Answer', 'type' => 'category'), true);?>
      </div>

      <div id="brand" style="display: none">
        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/brand_moderator/'.$this->store_id.'/spam', 'title' => 'Question', 'type' => 'brand'), true);?>

        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/brand_moderator/'.$this->store_id.'/spam/1', 'title' => 'Answer', 'type' => 'brand'), true);?>
      </div>

      <div id="product" style="display: none">
        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/product_moderator/'.$this->store_id.'/spam', 'title' => 'Question', 'type' => 'product'), true);?>

        <?php echo $this->load->view('moderate/spamList', array('url' => base_url().'moderator/product_moderator/'.$this->store_id.'/spam/1', 'title' => 'Answer', 'type' => 'product'), true);?>
      </div>

    </div>

  <div id="historySpam" class="spam-history" style="display: none" >
    <?php echo grid_title_html('History', 'close'); ?>
    <table id="history_list"></table>
    <div id="history_pager"></div>
     <?php
          echo render_grid('history_list', '', array(
            'caption' => '',
            'record_per_page' => $grid_limit,
            'pager' => '#history_pager',
            'sort_column' => 'user_id',
            'sort_order' => 'asc',
            'head' => array('User Id', 'Description', 'Date/Time'),
            'columns' => array(array(
                'name'  => 'User Id',
                'index' => 'user_id',
                'width' => 400
              ),array(
                'name'  => 'Description',
                'index' => 'description',
                'width' => 550
              ),array(
                'name'  => 'Date/Time',
                'index' => 'created_at',
                'width' => 196
              )
            )
          ));
        ?>
  </div>

  <?php else: ?>

    <div style="padding-top: 20px">
      Search name of the store, to see spam posts.<br/>
      <strong>Note:</strong> Stores having moderation type set to "None" will be shown in suggestions
    </div>
    
  <?php endif; ?>

</div>
