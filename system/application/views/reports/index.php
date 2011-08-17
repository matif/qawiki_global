<?php echo use_javascript('reports'); ?>
<script type="text/javascript">
  $(document).ready(function(){
    attach_pagination_events();
  });
</script>

<div>
  <a style="float:right; margin-bottom: 10px" class="qaw-buton-gray qaw-button" href="<?php echo base_url() . 'advancedReport/index/' . $store_id ?>"><span>Customize Report</span></a>
</div>
<div class="clear"></div>
<!--a href="javascript:;" rel="store|<?php echo $store_id ?>" class="view-report" style="float:right">(View Report)</a-->

<?php if (trim($store_id)) : ?>  
  
 

  <div class="moderate-tab-content">
    
    <?php if(isset($categories)) :?>
    
      <div class="" style="">
        <div class="heading_section  clearfix grid-header">
          <div class="head_rpt repo">Categories</div>
          <div class=""><a href="javascript:;"></a></div>
        </div>
        <div class="content_accordian" style="padding-bottom: 11px">
            <div id="categoryPag_data" class="">
              <?php echo $this->load->view('reports/categories', array("catagories" => $categories), true); ?>
            </div>      
            <div class="heading_section clearfix" style= "">
              <div class="paginition_area clearfix categoryPag_pagin" style="">
                <?php
                echo $this->load->view('components/_pagination', array_merge($category_params, array(
                          'page_element_id' => 'categoryPag'
                              )
                      ));?>
              </div>
            </div>
          </div>      
        </div>

      <?php elseif(isset($brands)):?>
    
        <div class="" style="">
          <div class="heading_section  clearfix grid-header">
            <div class="head_rpt repo">Brands</div>
            <div class=""><a href="javascript:;"></a></div>
          </div>      
          <div class="content_accordian" style="padding-bottom: 11px">
            <div id="brandPag_data" class="">
              <?php echo $this->load->view('reports/brands', array('brands' => $brands), true); ?>
            </div>
            <div class="heading_section clearfix" style= "">
              <div class="paginition_area clearfix  brandPag_pagin" style="">

                <?php
                    echo $this->load->view('components/_pagination', array_merge($brand_params, array(
                      'page_element_id' => 'brandPag'
                          )
                  ));?>
              </div>
            </div>
            
          </div>
        </div>
    
      <?php elseif(isset($products)):?>
      
        <div class="" style="">
          <div class="heading_section  clearfix grid-header">
            <div class="head_rpt repo">Products</div>
            <div class=""><a href="javascript:;"></a></div>
          </div>
          <div class="content_accordian" style="padding-bottom: 11px">
            <div id="productPag_data">
              <?php echo $this->load->view('reports/products', array('products' => $products), true); ?>
             </div> 
            <div class="heading_section clearfix" style= "">
              <div class="paginition_area clearfix  productPag_pagin" style="">
                <?php
                  echo $this->load->view('components/_pagination', array_merge($product_params, array(
                      'page_element_id' => 'productPag'
                          )
                  ));?>
              </div>
            </div>                       
          </div>
          
        </div>
    
      <?php endif;?>

      <?php echo $this->load->view('reports/filters', array(), true); ?>
    
    </div>
<?php endif; ?>

<input type="hidden" id="categoryPag_url" value="<?php echo base_url() . 'reports/categories/'.$this->store_id?>" />
<input type="hidden" id="brandPag_url" value="<?php echo base_url() . 'reports/brands/'.$this->store_id?>" />
<input type="hidden" id="productPag_url" value="<?php echo base_url() . 'reports/products/'.$this->store_id?>" />