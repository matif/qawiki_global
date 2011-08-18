<?php
class Pager
{
  var $anchors;
  var $total;

  function create($numrows, $starting, $recpage, $func = 'Call_ajax', $url='', $add_slash = true) {
    if ($add_slash)
      $url = $url . '/';
    else
      $url = $url . ',';
    //echo $url;

    $next = $starting + $recpage;
    $var = ((intval($numrows / $recpage)) - 1) * $recpage;
    $page_showing = intval($starting / $recpage) + 1;
    $total_page = ceil($numrows / $recpage);
    $anc = '';
    if ($numrows % $recpage != 0) {
      $last = ((intval($numrows / $recpage))) * $recpage;
    } else {
      $last = ((intval($numrows / $recpage)) - 1) * $recpage;
    }
    $previous = $starting - $recpage;
    if ($previous < 0) {
      $anc = "<<  Previous ";
    } else {
      $val = 0;
      if ($add_slash)
        $value = '"' . $url . $val . '"';
      else
        $value = $url . $val;
      $anc .= "<a href=' javascript: " . $func . "(" . $value . "  );'  ><<  </a>";
      if ($add_slash)
        $value = '"' . $url . $previous . '"';
      else
        $value = $url . $previous;
      $anc .= "<a href='javascript:" . $func . "(" . $value . " );'> Previous  </a>";
    }

    ################ If you dont want the numbers just comment this block ###############
    $norepeat = 4; //no of pages showing in the left and right side of the current page in the anchors
    $j = 1;
    $anch = '';
    for ($i = $page_showing; $i > 1; $i--) {
      $fpreviousPage = $i - 1;
      $page = ceil($fpreviousPage * $recpage) - $recpage;
      if ($add_slash)
        $value = '"' . $url . $page . '"';
      else
        $value = $url . $page;
      $anch = "<a href='javascript:" . $func . "( " . $value . " );'>$fpreviousPage  </a>, " . $anch;
      if ($j == $norepeat)
        break;
      $j++;
    }
    $anc .= $anch;
    $anc .= $page_showing . ", ";
    $j = 1;
    for ($i = $page_showing; $i < $total_page; $i++) {
      $fnextPage = $i + 1;
      $page = ceil($fnextPage * $recpage) - $recpage;
      if ($add_slash)
        $value = '"' . $url . $page . '"';
      else
        $value = $url . $page;
      $anc .= "<a href='javascript:" . $func . "( " . $value . " );'>$fnextPage  </a>";
      if ($i < $total_page - 1)
        $anc .= ',';
      $anc .= ' ';
      if ($j == $norepeat)
        break;
      $j++;
    }
    ############################################################
    if ($next >= $numrows) {
      $anc .= "Next  >>";
    } else {
      if ($add_slash)
        $value = '"' . $url . $next . '"';
      else
        $value = $url . $next;
      $anc .= "<a href='javascript:" . $func . "(" . $value . ");'> Next  </a>";
      if ($add_slash)
        $value = '"' . $url . $last . '"';
      else
        $value = $url . $last;
      $anc .= "<a href='javascript:" . $func . "(" . $value . ");'> >></a>";
    }
    $this->anchors = "<b>$anc</b>";

    //$this->total = "<svaluestrong>Page : $page_showing <i> Of  </i> $total_page . Total Records Found: $numrows</svaluestrong>";
    $this->total = $numrows;
  }

  function get_pagination($numrows, $starting, $recpage, $func = 'Call_ajax', $url='', $add_slash = true)
  {
    if($numrows > 0)
    {
      $this->create($numrows, $starting, $recpage, $func, $url, $add_slash);

      return $this->anchors;
    }

    return '';
  }
}