<?php

declare(strict_types=1);

namespace Juling\Foundation\Response;

use think\response\Json;

trait JsonResponse
{
    /**
     * @var string 客户端 ID
     */
    protected string $clientId = 'X-Client-Id';

    /**
     * 返回封装后的API数据到客户端
     *
     * @param mixed $data  要返回的数据
     * @param  array  $headers  发送的Header信息
     */
    protected function success(mixed $data = null, array $headers = []): Json
    {
        return $this->response([
            'code' => 0,
            'message' => 'ok',
            'data' => $data,
        ], $headers);
    }

    /**
     * 返回异常数据到客户端
     */
    protected function error(string $message = '', int $code = 40001, array $headers = []): Json
    {
        return $this->response([
            'code' => $code,
            'message' => $message,
            'data' => null,
        ], $headers);
    }

    /**
     * 返回 Json 数据格式
     */
    protected function response($data, array $headers = []): Json
    {
        $clientId = request()->header($this->clientId, md5($this->createSessionId()));

        return json($data, 200, array_merge($headers, [$this->clientId => $clientId]));
    }

    /**
     * 创建 Session ID
     */
    protected function createSessionId(): string
    {
        return bin2hex(pack('d', microtime(true)).pack('N', mt_rand()));
    }
}
