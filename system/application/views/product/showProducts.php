<?php echo use_javascript('products');?>
<?php echo use_javascript('link_products');?>
<script type="text/javascript">
  var store_id = <?php echo $this->store_id?>;
  var short_url_base = '<?php echo $this->config->item('shorten_url')?>';
</script>

<div class="rgt_850">
  <div class="header_rgt">
    

    <?php if($user_role != 'view'): ?>

      <a style="float: right" href="<?php echo base_url()?>post/postStyle/<?php echo $this->store_id; ?>"> Customize Your Widget</a>
      <span style="float:right"> &nbsp;| &nbsp; </span>
      <a style="float: right" href="<?php echo base_url()?>post/addPost/<?php echo $this->store_id; ?>"> Add More Products</a>

    <?php endif;?>

    <div class="clear"></div>
  </div>
  <input type="button" id="automaiton" value="Autonomous Products"/>
  <br/><br/>
  <div id="category">
    <h1>Categories</h1>

    <?php
      echo $this->load->view('product/categoryPagination', array(
          'category'        => $category,
          'user_role'       => $user_role,
          'permission'      => $permission,
          'category_pager'  => $category_pager
        ),
        true
      );
    ?>
    
  </div>
  
  <div class="header_rgt">
    <h1>Brands</h1>
    <div class="clear"></div>
  </div>
  <div id="brand">

    <?php
      echo $this->load->view('product/brandPagination', array(
          'brands'       => $brands,
          'user_role'    => $user_role,
          'permission'   => $permission,
          'brand_pager'  => $brand_pager
        ),
        true
      );
    ?>

  </div>
  
  <div class="header_rgt">
    <h1>Products</h1>
   
    <div class="clear"></div>
  </div>
  <div>
    <div id="product">
      <form method="post" action="" id="productsFrm">

        <?php
          echo $this->load->view('product/productPagination', array(
              'products'        => $products,
              'user_role'       => $user_role,
              'permission'      => $permission,
              'products_pager'  => $products_pager
            ),
            true
          );
        ?>

      </form>
    </div>
    
    <div class="header_rgt">
      <h1>Groups</h1>

      <?php if($user_role != 'view'): ?>

        <a href="javascript:;" id="makegroup">Add selected products to a Group</a>
        
      <?php endif;?>

      <div><label class="error hideDiv group_margin" id="group-error"></label></div>
      <div id="listGrp" class="hideDiv group_margin">
        <form method="post" class="constrain" id="groupFrm">
          <div>
            <label for="list_group">Select a group</label>
            <select id="list_group" name="group_id">
            <optgroup label='Create Group'>
              <option value = 'new_group' >Create new Group</option>
             </optgroup>
            <optgroup label='----------------------'></optgroup>
            <optgroup label='Select Group'>
              <?php foreach($this->grpData as $data):?>
                <option value ='<?php echo $data['qa_group_id']?>' ><?php echo $data['qa_name']?></option>
              <?php endforeach;?>
            </optgroup>
          </select>
          </div>
          <div id="groupInfo" class="group_margin" style="display: none">
            <div>
              <label for="group_name">Create a group</label> <input type="text" name="group_name" id="group_name" />
            </div>
            <input name="products" id ="products" type="hidden" value =""/>
            <div>
              <input type="button" value="Create Group" id="makeGroup"/>
            </div>
          </div>
        </form>
      </div>
      <br/>      
    </div>
    <div id="groups">
      <?php
        echo list_records_table($this->grpData, array(array(
            'text'     => 'qa_group_id',
            'heading'  => 'Group Id'
          ),array(
            'text'     => 'qa_name',
            'heading'  => 'Group Name'
          ),array(
            'rel'      => '{qa_group_id}',
            'class'    => 'viewGrp',
            'text'     => 'View',
            'heading'  => 'View'
          ),array(
            'rel'      => '{qa_group_id}',
            'class'    => 'deleteGrp',
            'text'     => 'Delete',
            'heading'  => 'Delete'
          )
        ));
      ?>
      
    </div>
    <div id="showGroupProducts"></div>
    
    <div id="viewQuestion"></div>
    <div id="answer"></div>    
      <div id="productQuestion" class="hideDiv">        
      </div>
  </div>  
</div>
