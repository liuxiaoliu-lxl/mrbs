<?php
namespace MRBS;

class ErrorCode{

  const SUCCESS = '200';
  const FAILED = '400';

  const ACTION_ERROR = '4001';

  const USER_UN_LOGIN = '5001';

  const ENTRY_DATE_ERROR = '6001';

  public static $message = [
    self::SUCCESS => '成功',

    self::ACTION_ERROR =>'方法不存在',

    self::USER_UN_LOGIN => '用户未登录',

    self::ENTRY_DATE_ERROR => '时间段'
  ];

}

class JsonResponse{

  public static function output($code, $msg = '', $data = []){
    if(array_key_exists($code, ErrorCode::$message)){
      if(empty($msg)){
        $msg = ErrorCode::$message[$code];
      }
    }
    echo json_encode([ 'code'  => $code, 'msg'   => $msg,'data'  => $data ]);
    exit();
  }

}
