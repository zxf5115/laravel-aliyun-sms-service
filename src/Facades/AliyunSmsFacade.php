<?php
namespace Zxf5115\Laravel\Aliyun\Sms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * 阿里云短信静态代理类
 */
class AliyunSmsFacade extends Facade
{
  protected static function getFacadeAccessor()
  {
    return 'Sms';
  }
}
