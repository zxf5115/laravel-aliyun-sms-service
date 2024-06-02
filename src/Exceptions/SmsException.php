<?php
namespace Zxf5115\Laravel\Aliyun\Sms\Exceptions;

use Exception;

/**
 * 短信异常类
 */
class SmsException extends Exception
{

  public function __construct($message = null, $code = 0)
  {
    parent::__construct($message, $code);
  }
}
