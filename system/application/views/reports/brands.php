<table cellspacing="0" cellpadding="0" border="0" width="1160" class="rpt_area">
  <tbody>
    <tr>
      <th width="30">&nbsp;</th>
      <th width="200" height="34">ID</th>
      <th width="200">Brand Id</th>
      <th width="200">Brand Name</th>
      <th width="200">View Reports</th>
    </tr>
    <?php if (count($brands)): ?>
      <?php foreach ($brands as $brand): ?>
        <tr>
          <td width="24">&nbsp;</td>
          <td align="center"><?php echo $brand["id"] ?></td>
          <td align="center"><?php echo $brand["qa_brand_id"] ?></td>
          <td><?php echo $brand["qa_brand_name"] ?></td>
          <td><a class="view-report" href="javascript:;" rel="brnad|<?php echo $brand["id"] ?>">View Report</a></td>
        </tr>
      <?php endforeach; ?> 
    <?php else: ?>
      <tr><td colspan="4" style="text-align: center">No Records Found</td></tr>
    <?php endif; ?>
  </tbody>
</table>



