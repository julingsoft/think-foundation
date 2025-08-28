<?php

declare(strict_types=1);

namespace Juling\Foundation\Routing;

use Juling\Foundation\Response\JsonResponse;
use think\App;
use think\Request;
use think\Validate;

abstract class Controller
{
    use JsonResponse;

    /**
     * 应用实例
     */
    protected App $app;

    /**
     * Request实例
     */
    protected Request $request;

    /**
     * 是否批量验证
     */
    protected bool $batchValidate = false;

    /**
     * 控制器中间件
     */
    protected array $middleware = [];

    /**
     * 构造方法
     *
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * @param  array  $data  数据
     * @param  array|string  $validate  验证器名或者验证规则数组
     * @param  array  $message  提示信息
     * @param  bool  $batch  是否批量验证
     */
    protected function validate(array $data, array|string $validate, array $message = [], bool $batch = false): bool
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = str_contains($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (! empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch();
        }

        return $v->failException()->check($data);
    }
}
