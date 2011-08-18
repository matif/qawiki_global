<?php

function resize_image($image_path, $new_image, $width = 152, $height = 152, $maintain_ratio = true)
{
  $config['image_library'] = 'gd2';
  $config['source_image'] = $image_path;
  $config['new_image'] = $new_image;
  $config['create_thumb'] = TRUE;
  $config['maintain_ratio'] = $maintain_ratio;
  $config['thumb_marker'] = "";  
  list($w, $h) = getimagesize($image_path);
  if($w < $width && $h < $height)
  {
    $width = $w;
    $height = $h;
  }

  $config['width'] = $width;
  $config['height'] = $height;

  $CI =& get_instance();
  
  if(!isset($CI->resize_count))
  {
    $CI->resize_count = 0;
  }
  else 
  {
    $CI->resize_count++;
  }

  $object_name = 'resize_obj_'.$CI->resize_count;
  
  $CI->load->library('image_lib', $config, $object_name);
  $CI->$object_name->resize();
  $CI->$object_name->clear();
  $CI->$object_name->initialize($config);
}

function save_product_image($image_url, $save_images_locally,$store_id)
{
  $name = ''; 
  if (isset($image_url))
  {
    if($save_images_locally == 1)
    {      
      $image_path = basename($image_url);
      $ext = explode('.', $image_path, 2);      
      $contents = @file_get_contents($image_url);
      $CI =& get_instance();       
      $path = $CI->config->item('root_dir') . '/uploads/stores/' . $store_id . '/products/';

      // create directory recursively
      mk_dir($path);

      // file name
      $name = time() . '.' . $ext[1];

      // write file to disk
      $fp = fopen($path . $name, 'w');
      fwrite($fp, $contents);
      fclose($fp);

      // http path
      $CI->load->helper('image');
      @resize_image($path.$name, $path.'t-'.$name);
      $name = base_url() . 'uploads/stores/' . $store_id . '/products/t-' . $name;
    }
    else
    {
      $name = $data[$this->fields['image url']];
    }
  }
  return $name;
}

/**
 * 
 * function save_image
 * 
 * @param <string> $image_name
 * @param <string> $base_path
 * 
 * save uploaded file
 */

function save_image($image_name, $base_path = '', $width = 152, $height = 152, $maintain_ratio = true)
{
  $CI =& get_instance();
  $file_path = null;

  // create directory recursively
  mk_dir($base_path);
  
  load_upload_library($base_path);

  if (trim($_FILES[$image_name]['name']))
  {
    $CI->upload->file_name = make_image_file_name($_FILES[$image_name]['name']);

    // upload logo image
    if (!$CI->upload->do_upload($image_name))
    {
      return array('error' => $CI->upload->display_errors());
    }

    $file_path = $CI->upload->file_name;

    // resize image
    $CI->load->helper('image');
    resize_image($base_path . $file_path, $base_path . 't-' . $CI->upload->file_name, $width, $height, $maintain_ratio);
  }
  
  return $file_path;
}

/**
 * 
 * function delete_images
 * 
 * @param <string> $base_path
 * @param <string> $file_name
 * 
 * delete image and its thumbnail
 * 
 */
function delete_images($base_path, $file_name, $keep_thumb = false)
{
  $old_file_path = $base_path . $file_name;

  if (file_exists($old_file_path) && is_file($old_file_path))
  {
    unlink($old_file_path);
  }

  $old_file_thumb = str_replace($file_name, 't-'.$file_name, $old_file_path);

  if (file_exists($old_file_thumb) && is_file($old_file_thumb) && !$keep_thumb)
  {
    unlink($old_file_thumb);
  }
}

/**
 * 
 * function process_image
 * 
 * @param <string> $base_path
 * @param <string> $file_name
 * 
 * delete image and its thumbnail
 * 
 */
function process_image(&$data, $key, &$error, $base_path, $file_name, $width = 152, $height = 152, $delete_original = false, $maintain_ratio = true)
{
  if (trim($_FILES[$file_name]['name']))
  {
    $response = save_image($file_name, $base_path, $width, $height, $maintain_ratio);

    if(!is_array($response))
    {
      if(isset($data[$key]) && trim($data[$key]))
      {
        delete_images($base_path, $data[$key]);
      }
      
      if($delete_original)
      {
        delete_images($base_path, $response, true);
      }
      
      $data[$key] = $response;
    }
    else
    {
      $error = $response;
    }
  }
}
