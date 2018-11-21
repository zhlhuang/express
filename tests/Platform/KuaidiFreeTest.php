<?php

/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/11/20
 * Time: 14:34.
 */

namespace Zhlhuang\Express\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Platform\KuaidiFree;

class KuaidiFreeTest extends TestCase
{
    public function testQueryExpressWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())// 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(KuaidiFree::class)->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);
        // 接着需要断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');
        $w->query('shunfeng', '123');
    }

    public function testExpressQuery()
    {
        $response = new Response(200, [], '{"message":"ok"}');
        $client = \Mockery::mock(Client::class);
        $client->allows()->get('http://www.kuaidi100.com/query', [
            'query' => [
                'type'   => 'shunfeng',
                'postid' => '123',
            ],
        ])->andReturn($response);

        $express = \Mockery::mock(KuaidiFree::class)->makePartial();
        $express->allows()->getHttpClient()->andReturn($client);
        $this->assertSame(['message' => 'ok'], $express->query('shunfeng', '123'));
    }
}
