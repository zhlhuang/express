<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-04-16
 * Time: 11:30.
 */

namespace Zhlhuang\Express\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Platform\JuheExp;

class JuheExpTest extends TestCase
{
    public function testQueryExpressWithInvalidConfigKey()
    {
        $express = new JuheExp();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('config key is required');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithInvalidConfigCustomer()
    {
        $express = new JuheExp([
            'key' => 'abc',
        ]);
        $this->expectException(InvalidArgumentException::class);
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())// 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(JuheExp::class)->makePartial();
        $w->setConfig([
            'key' => 'abc',
        ]);
        $w->allows()->getHttpClient()->andReturn($client);
        // 接着需要断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');
        $w->query('shunfeng', '123');
    }

    public function testExpressQuery()
    {
        $response = new Response(200, [], '{"reason":"ok","result":{"list":[]}}');
        $client = \Mockery::mock(Client::class);
        $client->allows()->get('http://v.juhe.cn/exp/index', [
            'query' => [
                'key' => 'abc',
                'com' => 'sf',
                'no'  => '123',
            ],
        ])->andReturn($response);

        $express = \Mockery::mock(JuheExp::class)->makePartial();
        $express->setConfig([
            'key' => 'abc',
        ]);
        $express->allows()->getHttpClient()->andReturn($client);
        $this->assertSame([
            'message'   => 'ok',
            'state'     => 0,
            'status'    => 200,
            'condition' => 'F00',
            'ischeck'   => 0,
            'com'       => 'shunfeng',
            'nu'        => '123',
            'data'      => [],
        ], $express->query('shunfeng', '123'));
    }
}
