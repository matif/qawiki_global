
<div class="content_dashboard">

  <div class="heading_section  clearfix">
    <div class="head setting">Customized Report</div>
  </div>

  <table cellpadding="0" cellspacing="0" border="0" class="rpt_area">

    <thead>
      <th width="15">&nbsp;</th>
      <th>Key Performance Indicator</th>
      <th>Daily Stats</th>
    </thead>

    <?php foreach($this->stats_data as $field => $stats): ?>

      <tr>

        <td>&nbsp;</td>
        
        <td><?php echo $this->mapping[$field]?></td>

        <?php foreach($stats as $key => $value): ?>

          <td><?php echo (!$value) ? 0 : $value?></td>

        <?php endforeach; ?>

      </tr>

    <?php endforeach; ?>

  </table>
</div>