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
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Platform\KuaidiCompany;

class KuaidiCompanyTest extends TestCase
{
    public function testQueryExpressWithInvalidConfigKey()
    {
        $express = new KuaidiCompany();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('config key is required');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithInvalidConfigCustomer()
    {
        $express = new KuaidiCompany([
            'key' => 'abc'
        ]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('config customer is required');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())// 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(KuaidiCompany::class)->makePartial();
        $w->setConfig([
            'customer' => 'abc',
            'key'      => 'abc'
        ]);
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
        $client->allows()->get('https://poll.kuaidi100.com/poll/query.do', [
            'query' => [
                'customer' => 'abc',
                'sign'     => strtoupper(md5('{"com":"shunfeng","num":"123"}'.'abc'.'abc')),
                'param'    => '{"com":"shunfeng","num":"123"}'
            ],
        ])->andReturn($response);

        $express = \Mockery::mock(KuaidiCompany::class)->makePartial();
        $express->setConfig([
            'customer' => 'abc',
            'key'      => 'abc'
        ]);
        $express->allows()->getHttpClient()->andReturn($client);
        $this->assertSame(['message' => 'ok'], $express->query('shunfeng', '123'));
    }

}
