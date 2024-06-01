<?php
namespace Zxf5115\Laravel\Basic\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

use Zxf5115\Laravel\Basic\Constant\Code;


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2024-05-27
 *
 * 成功信息响应函数
 *
 * @param array $data 响应数据
 * @return 成功信息
 */
function success($data = [], $message = '')
{
  $code = Code::SUCCESS;

  // 如果是响应代码
  if(is_int($data) && $data != 0 && $data != 1)
  {
    $data = Code::message($data);
  }

  return Response::json([
    'status' => $code,
    'message' => $message ?: Code::message($code),
    'data' => $data
  ]);
}


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2024-05-27
 *
 * 错误信息响应函数
 *
 * @param integer $code 错误代码
 * @return 错误信息
 */
function error($code = Code::ERROR)
{
  return Response::json([
    'status' => $code,
    'message' => Code::message($code)
  ]);
}


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2024-05-27
 *
 * 自定义消息响应函数
 *
 * @param integer $code 错误代码
 * @return 错误信息
 */
function message($message, $code = Code::CUSTOM_MESSAGE)
{
  return Response::json([
    'status'  => $code,
    'message' => $message
  ]);
}


/**
 * @author zhangxiaofei [<1326336909@qq.com>]
 * @dateTime 2024-05-27
 *
 * 日志响应函数
 *
 * @param object $exception 异常对象
 * @return 错误信息
 */
function record($exception)
{
  if(true == config('app.debug'))
  {
    dd($exception);
  }
  else
  {
    Log::debug($exception);
  }
}
