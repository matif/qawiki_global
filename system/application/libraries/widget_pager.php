<?

/*
  Developed by Reneesh T.K
  reneeshtk@gmail.com
  You can use it with out any worries...It is free for you..It will display the out put like:
  First | Previous | 3 | 4 | 5 | 6 | 7| 8 | 9 | 10 | Next | Last
  Page : 7  Of  10 . Total Records Found: 20
 */

class Widget_pager {

  var $anchors;
  var $total;

  function Widget_pager() {
    
  }

  function create($numrows, $starting = 0, $recpage = 5) {
    $next = $starting + $recpage;
    $var = ((intval($numrows / $recpage)) - 1) * $recpage;
    $page_showing = intval($starting / $recpage) + 1;
    $total_page = ceil($numrows / $recpage);

    if ($numrows % $recpage != 0) {
      $last = ((intval($numrows / $recpage))) * $recpage;
    } else {
      $last = ((intval($numrows / $recpage)) - 1) * $recpage;
    }
    $previous = $starting - $recpage;
    if ($previous < 0) {
      $anc = "First | Previous | ";
    } else {
      $anc = "<a href='javascript:;' onclick='qaw_widget.paginate(0);' class='qaw-link'>First</a> | ";
      $anc .= "<a href='javascript:;' onclick='qaw_widget.paginate($previous);' class='qaw-link'>Previous</a> | ";
    }

    ################If you dont want the numbers just comment this block###############
    $norepeat = 4; //no of pages showing in the left and right side of the current page in the anchors
    $j = 1;
    $anch = '';
    for ($i = $page_showing; $i > 1; $i--) {
      $fpreviousPage = $i - 1;
      $page = ceil($fpreviousPage * $recpage) - $recpage;
      $anch = "<a href='javascript:;' onclick='qaw_widget.paginate($page);' class='qaw-link'>$fpreviousPage</a> | " . $anch;
      if ($j == $norepeat)
        break;
      $j++;
    }
    $anc .= $anch;
    $anc .= $page_showing . " | ";
    $j = 1;
    for ($i = $page_showing; $i < $total_page; $i++) {
      $fnextPage = $i + 1;
      $page = ceil($fnextPage * $recpage) - $recpage;
      $anc .= "<a href='javascript:;' onclick='qaw_widget.paginate($page);' class='qaw-link'>$fnextPage</a> | ";
      if ($j == $norepeat)
        break;
      $j++;
    }
    ############################################################
    if ($next >= $numrows) {
      $anc .= "Next | Last ";
    } else {
      $anc .= "<a href='javascript:;' onclick='qaw_widget.paginate($next);' class='qaw-link'>Next</a> | ";
      $anc .= "<a href='javascript:;' onclick='qaw_widget.paginate($last);' class='qaw-link'>Last</a>";
    }
    $this->anchors = $anc;

    $this->total = "<svaluestrong>Page : $page_showing <i> Of </i> $total_page . Total Records Found: $numrows</svaluestrong>";
  }



  function get_pagination($numrows, $starting = 0, $recpage = 5)
  {
    if($numrows > 0)
    {
      $this->create($numrows, $starting, $recpage);
      
      return $this->anchors;
    }

    return '';
  }

}