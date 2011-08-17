
<?php use_javascript('format_csv') ?>

<?php $this->load->helper('format_csv') ?>

<?php $columns = csv_detect_columns($csv) ?>

<script type="text/javascript">
  var csv_fields = <?php echo json_encode($this->config->item('csv_fields')) ?>;
  var csv_required = ['product id', 'title', 'description'];
</script>

<?php if ($this->session->flashdata('error')): ?>

  <div class="error"><?php echo $this->session->flashdata('error') ?></div>
  <br/>

<?php endif; ?>
<form id="format-csv" action="<?php echo (isset($association) && trim($association)) ? base_url() . 'post/linkProducts/' . $store_id : base_url() . 'post/saveCsvData/' . $store_id ?>" method="post">

  <input type="hidden" name="cols_count" id="cols_count" value="<?php echo csv_columns_count($csv) ?>" />
  <input type="hidden" name="filename" id="filename" value="<?php echo $filename; ?>" />
  <div style="padding-bottom: 10px;">
<!--    <span class="rpt_content">Process CSV</span>-->
    
    <div class="btn_upload" style="margin: 0 0 0 0">
      <a id="graph_update" href="javascript:;" onclick="$('#format-csv').submit()">Process CSV</a>
    </div>
    
  </div>
    
  <div class="content_dashboard" style="">
    <div class="heading_section  clearfix">
      <div class="head mc">Format CSV</div>
    </div>    


    <div id="saved_cols_names">

      <?php echo csv_saved_columns($columns) ?>      
    </div>
    <div class="content_accordian">
      <div class="disp_content_white noborder nopad lnk_hover" style="overflow: auto">

        <table cellspacing="0" cellpadding="0" border="0" width="1160" class="rpt_area" >

          <thead>
            <tr class="format_csv">

              <?php echo csv_format_header($csv, $columns) ?>

            </tr>
          </thead>
          <tbody>
            <?php if(count($csv) != 0):?>
            <?php foreach ($csv as $key => $value): ?>

              <?php if ($key == 0)
                continue; ?>

              <tr>
                 <?php foreach ($value as $v): ?>
                    <td><?php echo htmlentities($v); ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
            <?php else:?>
              <tr><td colspan="12" align="center" style="text-align: center">No Record Found</td></tr>
            <?php endif;?>
          </tbody>

        </table>
        <input type="hidden" name="association" value="<?php echo $association ?>"/>

      </div>
    </div>
</form>