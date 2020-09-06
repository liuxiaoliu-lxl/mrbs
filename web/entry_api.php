<?php

namespace MRBS;

require "defaultincludes.inc";
require_once "functions_table.inc";

class EntryApi{

//  public function arList(){
//
//    $areas = get_area_names(false, true);
//    $ids = [];
//    foreach($areas as $k => $v){
//      $area_name = $areas[$k]['area_name'];
//      unset($areas[$k]['area_name']);
//      $areas[$k]['text'] = $area_name;
//      $areas[$k]['rooms'] = [];
//      $ids[] = $v['id'];
//    }
//
//    $rooms = get_rooms_list($ids);
//    foreach($rooms as $value){
//      $area_id = $value['area_id'];
//      $room_name = $value['room_name'];
//      $room_id = $value['room_id'];
//      unset($value['area_id'], $value['room_name'], $value['room_id']);
//      $value['id'] = $room_id;
//      $value['text'] = $room_name;
//      $areas[$area_id]['rooms'][] = $value;
//    }
//
//    return $areas;
//  }

  public function areaList(){

    $area = get_area_names(false, true);
    foreach($area as $k => $v){
      $area[$k]['text'] = $v['area_name'];
      unset($area[$k]['area_name']);
    }

    return $area;
  }

  public function roomList(){
    $area = get_form_var('area', 'int');
    $rooms= get_room_names($area, false, true);
    foreach($rooms as $k => $v){
      $rooms[$k]['text'] = $v['room_name'];
      unset($rooms[$k]['room_name']);
    }
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

    return $entry_list;
  }

  public function entryLists(){

    $room = get_form_var('room', 'int');
    $date = get_form_var('date', 'string');

    $start_time = strtotime($date);
    $year = date('Y', $start_time);
    $month = date('m', $start_time);
    $day = date('d', $start_time);

    $start_first_slot = get_start_first_slot($month, $day, $year);
    $end_last_slot = get_end_last_slot($month, $day, $year);

    $entry_list = get_entries_by_room($room, $start_first_slot, $end_last_slot);

    $index_array = array_fill(0, 48, '0');

    $new_entry_list = [];

    $end_max = 24 * 60 * 60;
    $date_time = strtotime($date);

    foreach($entry_list as $k => $v){
      $id = $v['id'];

      $start_time = $v['start_time'] < $date_time ? $date_time : $v['start_time'];
      $end_time = ($v['end_time'] > $date_time + $end_max) ? $date_time + $end_max - 1 : $v['end_time'];

      //开始时间
      $s_hour = date('H', $start_time);
      $s_min  = date('i', $start_time);
      $start_index = ($s_hour * 60 + $s_min) / 30;

      //结束时间
      $e_hour = date('H', $end_time);
      $e_min  = date('i', $end_time);
      $min_count = $e_hour * 60 + $e_min;
      $end_index = ($min_count > $end_max ? $end_max : $min_count) / 30;

      for ($i = $start_index; $i < $end_index; $i++){
        $index_array[$i] = $id;
      }

      $new_entry_list[$id] = $v;
    }

    $result = [];

    $index_count = 0;
    $pre = 0;

    $max_index = count($index_array) - 1;

    foreach($index_array as $k => $v){

      if($k == 0){ $pre = $v; }

      if($pre == $v){
        $index_count++;
        if($max_index != $k){
          continue;
        }
      }

      $result[] = [
        'type'  => $pre ? '1' : '-1',
        'count' => $index_count,
        'data'  => $pre ? $new_entry_list[$pre] : [],
      ];

      //如果最后一次与前一个不相等则重新保存一次
      if($max_index == $k && $pre != $v){
        $pre = $v;
        $index_count = 1;
        $result[] = [
          'type'  => $pre ? '1' : '-1',
          'count' => $index_count,
          'data'  => $pre ? $new_entry_list[$pre] : [],
        ];
      }

      $pre = $v;
      $index_count = 1;

    }

//    $total = 0;
//    foreach($result as $k1 => $v1){
//      $total += $v1['count'];
//    }
//
//    echo $total;exit;

    return $result;
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
    case 'entryss':
      $result['data'] = $entryApi->entryLists();
      break;
//    case 'arList':
//      $result['data'] = $entryApi->arList();
//      break;
  }
  echo json_encode($result);
}
catch (Exception $e){
  $result = ['code' => 500, 'msg' => $e->getMessage(), 'data' => []];
  echo json_encode($result);
}

