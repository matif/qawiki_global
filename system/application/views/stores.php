
<?php echo use_javascript("highcharts/highcharts.js") ?>
<?php echo use_javascript("highcharts/graph_bar.js") ?>

<script type="text/javascript">  
  $(document).ready(function(){
    attach_pagination_events();
  });
  function confirmation(id)
  {
    var res = confirm('Are you sure, you want to delete this store?');
    if(res) {
      $.post(base_url+'post/deleteStore/'+id,function(){
        window.location.reload();
      });
    }
    $('#storeId').val(id);
  }

  function store_paginate(url)
  {
    window.location.href = url;
  }

  $(document).ready(function(){    
     
    $(".accordian_open").live('click', function(){
      var store_id = $(this).attr("rel");
      var self = this;
      $.post(base_url+"/dashboard/store_details/"+store_id, function(data){
        $(self).parent().after(data);
      });
    });
  
    $(".accordian_close").live('click', function(){
      $(this).parent().next().remove();
    });  
   
  });

</script>

<div class="home_heading clearfix">
  <div class="heading">Q&amp;A Home</div>
  <div class="cart_section"><a href="<?php echo base_url() ?>post/createStore">Add<br />Q&amp;A</a></div>
</div>
<div id="storePag_data">
  <?php echo $this->load->view('partials/_dashboard', array("members" => $members), true)?>
</div>
<div id="">
  <div class="heading_section clearfix">
    <div class="paginition_area clearfix categoryPag_pagin storePag_pagin" style="padding-top:11px">

      <?php
      echo $this->load->view('components/_pagination', array_merge($store_params, array(
                'page_element_id' => 'storePag'
                    )
            ));?>
    </div>
  </div>
</div>

<input type="hidden" id="storePag_url" value="<?php echo base_url() . 'dashboard/index/ajax'?>" />

