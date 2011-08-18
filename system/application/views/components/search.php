<?php

$fields = array(array(
    'label'   => 'Filter List by',
    'class'   => 'filter-heading'
  ),array(
    'type'    => 'select',
    'id'      => 'filter_type',
    'name'    => 'filter_type',
    'value'   => (isset($this->filter_type) ? $this->filter_type : ''),
    'options' => array(
      ''         => 'All',
      'category' => 'Category',
      'brand'    => 'Brand',
      'product'  => 'Product',
    )
  ), array(
    'type'  => 'text',
    'id'    => 'filter_text',
    'name'  => 'filter_text',
    'value' => (isset($this->filter_text) && $this->filter_text != -1 ? $this->filter_text : '')
  ), array(
    'type'  => 'submit',
    'name'  => 'search_btn',
    'value' => 'Search',
    'class' => 'search-btn'
  ));

echo search_component($fields, '', 'post', 'constrain inline-constrain');