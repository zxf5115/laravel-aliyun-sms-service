<?php
namespace Zxf5115\Laravel\Aliyun\Sms\Services;

use Zxf5115\Laravel\Aliyun\Sms\Exceptions\SmsException;

/**
 * 阿里云短信服务类
 */
class AliyunSmsService
{
  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 验证码校验方法
   *
   * @param [type] $mobile 手机号码
   * @param [type] $sms_code 验证码
   * @param [type] $scene 场景
   * @return [type]
   */
  public function verify($mobile, $sms_code, $scene)
  {
    // 验证码缓存前缀
    $prefix = config('zxf5115.sms.key.sms_code');

    // 组装验证码缓存key值
    $key = "{$prefix}_{$scene}_{$mobile}";

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


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 发送验证码
   *
   * @param [type] $mobile 待发送手机号码
   * @param [type] $scene 验证码场景
   * @param [type] $expire 验证码到期时间
   * @return [type]
   */
  public function execute($mobile, $scene, $expire = 600)
  {
    // 生成验证码
    $sms_code = $this->generate($mobile, $scene, $expire);

    // 获取短信配置信息
    $config = $this->setConfig();

    $sms = new EasySms($config);

    // 设置短信发送内容
    $content = $this->setContent($sms_code);

    $sms->send($mobile, $content);
  }




  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 生成验证码方法
   *
   * @param [type] $mobile 待发送手机号码
   * @param [type] $scene 验证码场景
   * @param [type] $expire 验证码到期时间
   * @return [type]
   */
  protected function generate($mobile, $scene, $expire)
  {
    // 验证码缓存前缀
    $prefix = config('zxf5115.sms.key.sms_code');

    // 组装验证码缓存key值
    $key = "{$prefix}_{$scene}_{$mobile}";

    // 如果验证码缓存未过期，抛出验证码未过期异常
    if(Redis::exists($key))
    {
      // 验证码还未过期，请勿重复申请
      throw new SmsException(__('zxf5115::sms.code_not_expired'));
    }

    // 生成6位验证码，不足左侧补0
    $sms_code = str_pad(rand(1, 999999), 6, 0, STR_PAD_LEFT);

    // 记录验证码
    Redis::set($key, $sms_code);

    // 设置验证码有效期时间
    Redis::expire($key, $expire);

    return $sms_code;
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 设置短信网关
   *
   */
  protected function setConfig()
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
   * @dateTime 2024-06-02
   *
   * 设置短信发送内容
   *
   * @param [type] $code 短信验证码
   */
  protected function setContent($code)
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
