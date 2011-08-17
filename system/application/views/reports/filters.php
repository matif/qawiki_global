<script type="text/javascript" src="<?php echo base_url() ?>js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>js/highcharts/graph_line.js"></script>

<script type="text/javascript">

  var line_graph;
  var base_route = '<?php echo base_url()?>charts/clicks';
  var item_id = <?php echo $store_id?>;
  var item_type = 'store';
  
</script>


<div class="content_dashboard">

  <div class="heading_section  clearfix grid-header">
    <div class="head_rpt repo">View Report</div>
    <div class="accordian_close"><a href="javascript:;"></a></div>
  </div>
  
  <div class="content_accordian">
    <div class="disp_content_white noborder">
      
      <form action=""  method="post" class="constrain">
        
        <div class="row_dat">
          <div class="lbel">From:</div>
          <div class="lbl_inpuCnt">
            <input type="text" id="start_date" class="account_med"  value = "<?php echo date("Y-m-d", $this->date[0])?>"/>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div class="lbel">To:</div>
          <div class="lbl_inpuCnt">
            <input type="text" id="end_date" class="account_med" value="<?php echo date("Y-m-d", $this->date[1])?>"/>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div class="lbel">Type:</div>
          <div class="lbl_inpuCnt" style="width: auto">
            <input type="checkbox" name="action_type[]" value="widget_load" /> &nbsp;Widget Load&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="action_type[]" value="question" /> &nbsp;Questions&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="action_type[]" value="answer" /> &nbsp;Answers&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="action_type[]" value="email" /> &nbsp;Email Clicks
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row_dat">
          <div class="btn_upload" style="margin: 0 0 0 120px"><a href="javascript:;" id="graph_update">Update</a></div>
        </div>
        
        <div id="err_rpt" class="graph-no-data error" style="display: none; padding-top: 30px;">No data found for this item</div>
        
        <div id="report_graph"></div>
        
      </form>
      
    </div>
  </div>
</div>