<?php
/* 
 * qaExcelExport class
 * 
 * author - Kashif Ali
 *
 */

class qaExcelExport
{
  private $xls = '';
  private $col = 0;
  private static $row = 0;
  private static $first = true;

 /**
  * function export
  *
  * @param <array> $data
  * @param <string> $title
  * @param <array> $headings
  *
  */
  public static function export($data, $title, $exit = true)
  {
    $excel = new qaExcelExport();
    
    // set title
    $excel->setTitle($title);

    if(count($data) > 0)
    {
      // data
      foreach($data as $key => $value)
      {
        $excel->nextRow();

        foreach($value as $k => $v)
        {
          if(is_numeric($v))
          {
            $excel->writeNumber($v);
          }
          else
          {
            $excel->writeLabel($v);
          }
        }
      }
    }
    
    // to avoid overwriten row for multiple calls
    $excel->nextRow(3);

    // flush xls to output
    $excel->flush($exit);
  }

/**
  * function exportByKeys
  *
  * @param <array> $data
  * @param <string> $title
  * @param <array> $headings
  *
  */
  public static function exportByKeys($data, $title, $headings)
  {
    $excel = new qaExcelExport();

    // set title
    $excel->setTitle($title);

    // make headings
    $excel->makeHeadings($data, $headings);

    if(count($data) > 0)
    {
      $array_keys = array_keys($data);
      $count = count($data[$array_keys[0]]);

      // data
      for($i = 0; $i < $count; $i++)
      {
        $excel->nextRow();

        foreach($array_keys as $key)
        {
          $value = $data[$key][$i];
          
          if(is_numeric($value))
          {
            $excel->writeNumber($value);
          }
          else
          {
            $excel->writeLabel($value);
          }
        }
      }
    }

    // flush xls to output
    $excel->flush();
  }  
  
 /**
  * function writeNumber
  *
  * @param <int> $value
  *
  */
  private function writeNumber($value)
  {
    $value = round($value, 2);

    $this->xls .= pack("sssss", 0x203, 14, self::$row, $this->col, 0x0);
    $this->xls .= pack("d", $value);

    $this->col++;
  }

 /**
  * function writeLabel
  *
  * @param <string> $value
  *
  */
  private function writeLabel($value)
  {
    $length = strlen($value);
    $this->xls .= pack("ssssss", 0x204, 8 + $length, self::$row, $this->col, 0x0, $length);
    $this->xls .= $value;

    $this->col++;
  }

 /**
  * function resetColumn
  *
  * reset the column attribute to 0
  *
  */
  private function resetColumn()
  {
    $this->col = 0;
  }

 /**
  * function resetColumn
  *
  * @param <int>   $increment
  * 
  * increment the row attribute by given parameter and reset the column
  *
  */
  private function nextRow($increment = 1)
  {
    self::$row += $increment;
    $this->resetColumn();
  }

 /**
  * function setTitle
  *
  * @param <string> $title
  *
  */
  private function setTitle($title)
  {
    $this->resetColumn();
    $this->writeLabel($title);
    $this->nextRow();
  }

 /**
  * function makeHeadings
  *
  * @param <array> $data
  * @param <array> $headings
  *
  */
  private function makeHeadings($data, $headings)
  {
    $this->resetColumn();

    $keys = array_keys($data);

    foreach($keys as $key)
    {
      $this->writeLabel($key);
    }
  }

  private function flush($exit = true)
  {
    if(self::$first)
    {
      self::header();
      
      #BOF
      $this->xls = pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0).$this->xls;
      
      self::$first = false;
    }

    echo $this->xls;
    
    if($exit)
    {
      self::eof();
    }
  }
  
  /**
   * function eof
   * 
   * 
   */
  public static function eof()
  {
    #EOF
    echo pack("ss", 0x0A, 0x00);

    exit();
  }
  
  public static function header($type = "")
  {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    
    if($type != "xls")
    {
      header("Content-Disposition: attachment;filename=orderlist.html ");
    }
    else
    {
      header("Content-Disposition: attachment;filename=orderlist.xls ");
    }
    
    header("Content-Transfer-Encoding: binary ");
  }
}