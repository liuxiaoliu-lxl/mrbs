<?php

namespace MRBS;

require "defaultincludes.inc";
require_once "functions_table.inc";

class EntryApi{

  public function areaList(){
    return get_area_names(false, true);
  }

  public function roomList(){
    $area = get_form_var('area', 'int');
    $rooms= get_room_names($area, false, true);
    return empty($rooms) ? [] : $rooms;
  }

  public function entryList(){
    $room = get_form_var('room', 'int');
    $date = get_form_var('date', 'string');

    $start_time = strtotime($date);
    $year = date('Y', $start_time);
    $month = date('m', $start_time);
    $day = date('d', $start_time);


    $start_first_slot = get_start_first_slot($month, $day, $year);
    $end_last_slot = get_end_last_slot($month, $day, $year);

    $entry_list = get_entries_by_room($room, $start_first_slot, $end_last_slot);

    foreach($entry_list as $k => $v){
      $entry_list[$k] = $v;
    }

    return $entry_list;
  }

}

$result = ['code' => 0, 'msg' => 'success', 'data' => []];

$action = get_form_var('action', 'string');

try {
  $entryApi = new EntryApi();
  switch ($action){
    case 'areas':
      $result['data'] = $entryApi->areaList();
      break;
    case 'rooms':
      $result['data'] = $entryApi->roomList();
      break;
    case 'entrys':
      $result['data'] = $entryApi->entryList();
      break;
  }
  echo json_encode($result);
}
catch (Exception $e){
  $result = ['code' => 500, 'msg' => $e->getMessage(), 'data' => []];
  echo json_encode($result);
}

