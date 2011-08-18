<table cellspacing="0" cellpadding="0" border="0" width="1160" class="rpt_area">
  <tbody>
    <tr>
      <th width="24">&nbsp;</th>
      <th width="200" height="34">ID</th>
      <th width="200">Product Id</th>
      <th width="200">Product Name</th>
      <th width="200">View Reports</th>
    </tr>    
    <?php if (count($products) != 0): ?>
      <?php foreach ($products as $product): ?>
        <tr>
          <td width="24">&nbsp;</td>
          <td align="center"><?php echo $product["id"] ?></td>
          <td align="center"><?php echo $product["qa_product_id"] ?></td>
          <td><?php echo $product["qa_product_title"] ?></td>
          <td><a class="view-report" href="javascript:;" rel="product|<?php echo $product["id"] ?>">View Report</a></td>
        </tr>
      <?php endforeach; ?>          
    <?php else: ?>
      <tr><td colspan="5" style="text-align: center">No Record Founds</td></tr>
    <?php endif; ?>
  </tbody>
</table>


