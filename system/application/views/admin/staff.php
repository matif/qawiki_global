
<script type="text/javascript" src="<?php echo base_url() ?>js/admin/staff.js"></script>

<h1 class="page-heading">Staff</h1>

<div class="actions">
  <span class="button-link"><a href="javascript:;" id="add-new">Add New</a></span>
</div>

<table cellpadding="0" cellspacing="0" class="data-list">

  <thead>
    <tr>
      <th width="7%">Sr. #</th>
      <th width="39%">Name</th>
      <th width="39%">Email</th>
      <th width="15%">Actions</th>
    </tr>
  </thead>

  <tbody>

    <?php if($users) :?>

      <?php foreach($users as $key => $user) : ?>

        <tr>
          <td align="center"><?php echo $key + 1?></td>
          <td align="center"><?php echo $user['name']?></td>
          <td align="center"><?php echo $user['email']?></td>
          <td align="center">
            <a href="javascript:;" class="edit-record" rel="<?php echo $user['qa_user_id']?>">Edit</a> |
            <a href="javascript:;" class="delete-record" rel="<?php echo $user['qa_user_id']?>">Delete</a>
          </td>
        </tr>

      <?php endforeach;?>

    <?php else:?>

        <tr>
          <td colspan="4" align="center">No record found!</td>
        </tr>

    <?php endif;?>

  </tbody>

</table>

<form id="add-form" action="" onsubmit="return save_staff();" class="constrain" style="display: none">
  <h2 class="heading">Add Staff</h2>
  <div>
    <label for="user_name">Name:</label>
    <input type="text" name="user_name" id="user_name" class="required" />
  </div>

  <div>
    <label for="user_email">Email:</label>
    <input type="text" name="user_email" id="user_email" class="required email" />
  </div>

  <div>
    <label for="user_password">Password:</label>
    <input type="password" name="user_password" id="user_password" class="required" />
  </div>

  <div>
    <label>&nbsp;</label>
    <input type="submit" value="Save" />
    <input type="hidden" value="0" name="user_id" id="user_id" />
  </div>
</form>