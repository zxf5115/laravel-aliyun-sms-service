<?php
namespace Zxf5115\Laravel\Aliyun\Sms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

use Zxf5115\Laravel\Aliyun\Sms\Services\AliyunSmsService;

/**
 * 阿里云短信服务提供器类
 */
class AliyunSmsServiceProvider extends ServiceProvider implements DeferrableProvider
{
  /**
   * 如果延时加载，$defer 必须设置为 true
   */
  protected $defer = true;

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    // 注册单例服务
    $this->app->singleton('Sms', function($app){
        return new AliyunSmsService;
    });

    // 设置别名
    $this->app->alias('Sms', AliyunSmsService::class);
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    // 设置配置信息
    $this->setupConfig();
  }


  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return [AliyunSmsService::class];
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 设置配置信息
   *
   * @return [type]
   */
  private function setupConfig()
  {
    $path = __DIR__ . '/../../config/config.php';

    // 加载配置文件
    $this->publishes([
      $path => config_path('zxf5115.php')
    ], 'zxf5115');

    $this->mergeConfigFrom($path, 'zxf5115');
  }


  /**
   * @author zhangxiaofei [<1326336909@qq.com>]
   * @dateTime 2024-06-02
   *
   * 设置语言信息
   *
   * @return [type]
   */
  private function setupLanguage()
  {
    $path = __DIR__ . '/../../lang';

    // 加载语言文件
    $this->loadTranslationsFrom($path, 'zxf5115');
  }
}
