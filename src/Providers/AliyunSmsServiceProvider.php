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

  }


  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return ['sms', AliyunSmsService::class];
  }
}
