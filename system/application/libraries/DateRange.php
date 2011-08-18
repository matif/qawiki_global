<?php

/**
 * Citizennet date range class
 *
 * - Handles the different types of date selectors, error handling for invalid dates
 *
 */
class DateRange {
  /*
   * Calendar function
   *
   * @element      name of element or image url
   * @type         type of element
   * @config       optional - configuration attributes
   *
   * return calendar attached to desired element
   */

  public static function calendar($element, $type = 'input', $config = array(), $plugin = 'datepicker') {
    $calendar = '';
    $imgSrc = sfConfig::get('app_calendar_image');

    if (isset($config['label']) && trim($config['label']))
      $calendar = '<label ' . (isset($config['labelCss']) ? 'class="' . $config['labelCss'] . '"' : '') . '>' . $config['label'] . '</label>';

    if (in_array($type, array('input', 'combine')))
      $calendar .= '<input type="text" value="' . (isset($config['value']) ? $config['value'] : '') . '"';
    elseif ($type == 'image') {
      $imgSrc = $element;
      $calendar .= '<input type="hidden"';
      $element = explode('/', $element);
      $element = $element[count($element) - 1];
      $element = explode('.', $element);
      $element = $element[0];
    }

    $id = (isset($config['id'])) ? $config['id'] : 'id_' . $element;

    $calendar .= ' name="' . $element . '" id="' . $id . '" ' . (isset($config['class']) ? 'class="' . $config['class'] . '"' : '');
    $calendar .= ' />';

    $calendar .= '<script type="text/javascript">
      var options = {dateFormat: "yy-mm-dd"' . (!isset($config['max']) || $config['max'] ? ', maxDate: "' . date('Y-m-d') . '"' : '') . '};';

    if (in_array($type, array('image', 'combine')))
      $calendar .= 'options.buttonImage = "' . $imgSrc . '"; options.buttonImageOnly = true; options.showOn = "button";';

    if (isset($config['update']))
      $calendar .= 'options.altField = "#' . $config['update'] . '"';

    if (isset($config['compare']))
      $calendar .= 'options.onClose = function(value){
          if(Date.parse($(\'#' . $config['compare'] . '\').val()) ' . ($config['operator'] ? $config['operator'] : '>') . ' Date.parse(value)) {
            alert("Start date should be less than or equal to end date");
            $(this).val("");
          }
	}';

    $calendar .= '$("#' . $id . '").' . $plugin . '(options);
        $("#' . (isset($config['update']) ? $config['update'] : $id) . '").keypress(function(event) {event.preventDefault();});
      </script>';

    return $calendar;
  }

  /*
   * Milestone Select function
   *
   * @milestones      list of milestones
   * @callback        optional - function to call on select change
   *
   * return dropdown list for milestones with optional callback attached
   */

  public static function milestone_select($milestones, $callback = '') {
    $select = '<select ' . (trim($callback) ? 'onchange="' . $callback . '" ' : '') . '>
      <option value="">milestone</option>';

    if ($milestones) {
      foreach ($milestones as $milestone)
        $select .= '<option value="' . $milestone['date'] . '">' . $milestone['name'] . '</option>';
    }

    $select .= '</select>';

    return $select;
  }

  /*
   * Today date function
   *
   * @update      element to update
   *
   * return today link
   */

  public static function today_date_link($update, $class = '') {
    return '<a href="javascript:;" onclick="$(\'#' . $update . '\').val(\'' . date('Y-m-d') . '\')" ' . (trim($class) ? 'class="' . $class . '"' : '') . '>Today</a>';
  }

  /*
   * Flush debug info to browser
   *
   */

  public function validateDates($start_date = false, $end_date = false) {
    $currentTime = strtotime(gmdate('Y-m-d', time()));

    if (!trim($start_date) && !trim($end_date)) {
      $end_date = $currentTime;
      $start_date = strtotime('-1 month', time());
    } elseif (!trim($end_date)) {
      $end_date = $currentTime;
    } elseif (!trim($start_date)) {
      $start_date = strtotime('-1 month', time());
    } else {
      $start_date = strtotime($start_date);
      $end_date = strtotime($end_date);
    }

    //if ($end_date > $currentTime)
      //$end_date = $currentTime;

    return array($start_date, $end_date);
  }

}

