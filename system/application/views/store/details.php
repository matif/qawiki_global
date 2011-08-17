<div class="content_accordian clearfix">
  <div class="lft_accordian">
    <div class="round_corners">
      <div class="corners clearfix">
        <div class="tl"></div>
        <div style="width:534px;" class="corners_center"></div>
        <div class="tr"></div>
      </div>
      <div class="content_grey">
        <div class="range"><?php echo $pending['question']?></div>
        <div class="repriced_items">Questions Pending Moderation</div>
      </div>
      <div class="corners clearfix">
        <div class="bl"></div>
        <div style="width:534px;" class="corners_center"></div>
        <div class="br"></div>
      </div>
    </div>
    <div class="round_corners">
      <div class="corners clearfix">
        <div class="tl"></div>
        <div style="width:534px;" class="corners_center"></div>
        <div class="tr"></div>
      </div>
      <div class="content_grey">
        <div class="range"><?php echo $pending['answer']?></div>
        <div class="repriced_items">Answers Pending Moderation</div>
      </div>
      <div class="corners clearfix">
        <div class="bl"></div>
        <div style="width:534px;" class="corners_center"></div>
        <div class="br"></div>
      </div>
    </div>
  </div>
  
  <div class="rgt_accordian">
    
    <div class="dash-graph-caption">Question Volume</div>
    
    <div id="graph_<?php echo $store_id?>" class="dash-graph">
    
      <script type="text/javascript">
        $("#graph_<?php echo $store_id?>").css({width: 500, height: 300});
        var <?php echo 'chart_'.$store_id?> = new cnBarGraph({
          container: 'graph_<?php echo $store_id?>',
          data: <?php echo json_encode($chart_data)?>
        }).render();
      </script>
    
    </div>
  </div>
</div>