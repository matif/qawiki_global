<?php if($members):?>
  <?php foreach ($members as $member) : ?>

    <div class="content_dashboard">
      <div class="heading_section  clearfix">
        <div class="store_name_desc">
          <ul>
            <li class="store_"><a href="#"><?php echo $member['qa_store_name'] ?></a></li>
            <li><a href="<?php echo base_url() . 'moderate/index/' . $member['qa_store_id'] ?>">Moderate</a></li>

            <?php if ($member['role'] != 'view') : ?>
              <li><a href="<?php echo base_url() . 'post/createStore/edit/' . $member['qa_store_id'] ?>">Settings</a></li>
            <?php endif; ?>

            <li><a href="<?php echo base_url() . 'reports/index/' . $member['qa_store_id'] ?>">Reports</a></li>          
          </ul>
        </div>
        <div class="dashboard_listing">
          <ul>
            <li class="selected"><a href="<?php echo base_url() ?>moderate/index/<?php echo $member['qa_store_id'] ?>" class="qus">Questions</a></li>            
            <li><a href="<?php echo base_url() ?>teammembers/index/0/<?php echo $member['qa_store_id'] ?>" class="usr">Users</a></li>
          </ul>
        </div>
        <div class="accordian_open" rel="<?php echo $member['qa_store_id']; ?>"><a href="javascript:;"></a> </div>
      </div>
    </div>

  <?php endforeach; ?>
<?php else:?>

  <div class="content_dashboard">
      <div class="heading_section  clearfix">
        <div class="store_name_desc">
          <ul>
            <li class="store_" style="text-align: center; padding-top: 10px; padding-left: 450px;">NO Record Found</li>
          </ul>
        </div>
      </div>
  </div>
<?php endif;?>
