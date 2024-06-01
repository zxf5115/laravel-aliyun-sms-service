<?php
namespace Zxf5115\Laravel\Aliyun\Sms\Services;

/**
 * 响应消息服务类
 */
class AliyunSmsService
{
  // 验证码
  private $_code = null;


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-04-13
   *
   * 发送验证码
   *
   * @param [type] $mobile 手机号码
   * @param [type] $code 验证码
   * @return [type]
   */
  public function send($mobile, $code)
  {
    try
    {
      // 获取短信配置信息
      $config = self::setConfig();

      $sms = new EasySms($config);

      // 设置短信发送内容
      $content = self::setContent($code);

      $sms->send($mobile, $content);
    }
    catch (NoGatewayAvailableException $e)
    {
      $message = $e->getException('aliyun')->getMessage();

      // 记录异常信息
      record($message);

      return false;
    }
  }


  /**
   * Handle the event.
   *
   * @param  CodeEvent  $event
   * @return void
   */
  public function verify($mobile, $sms_code, $type)
  {
    try
    {
      $key = config('key.redis.sms_code') . '_' . $type . '_' . $mobile;

      // 获取真实验证码
      $real_sms_code = Redis::get($key);

      // 如果真实验证码不存在
      if(empty($real_sms_code))
      {
        return false;
      }

      // 验证码错误
      if($real_sms_code != $sms_code)
      {
        return false;
      }

      Redis::del($key);

      return true;
    }
    catch(\Exception $e)
    {
      // 记录异常信息
      record($e);

      return false;
    }
  }




  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-01
   *
   * 生成验证码并保存到redis（10分钟自动失效）
   *
   * @param [type] $mobile 待发送手机号码
   * @param [type] $type 验证码类型
   * @param [type] $expire 验证码到期时间
   * @return [type]
   */
  protected function generate($mobile, $type, $expire = 600)
  {
    $key = config('key.redis.sms_code') . '_' . $type . '_' . $mobile;

    // 删除之前的验证码
    if(Redis::exists($key))
    {
      throw new SmsException('请勿重复申请');
    }

    // 生成6位验证码，不足左侧补0
    $this->_code = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);

    // 记录验证码
    Redis::set($key, $this->_code);

    // 设置验证码时间为10分钟
    Redis::expire($key, $expire);
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-01
   *
   * 设置短信网关
   *
   */
  protected static function setConfig()
  {
    $config = config('easysms');

    // 短信公钥
    $access_key = configure('sms_access_key');

    // 短信私钥
    $access_secret = configure('sms_access_secret');
    // 短信签名
    $sign_name = configure('sms_sign_name');

    $gateways = $config['default']['gateways'][0];

    $config['gateways'][$gateways]['access_key_id'] = $access_key;
    $config['gateways'][$gateways]['access_key_secret'] = $access_secret;
    $config['gateways'][$gateways]['sign_name'] = $sign_name;

    return $config;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-01
   *
   * 设置短信发送内容
   *
   * @param [type] $code 短信验证码
   */
  protected static function setContent($code)
  {
    $template_id = configure('sms_template_id');

    $response = [
      'template' => $template_id,
      'data' => [
        'code' => $code
      ]
    ];

    return $response;
  }
}
