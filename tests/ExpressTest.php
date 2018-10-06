<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/10/6
 * Time: 11:24.
 */

namespace Zhlhuang\Express\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Express;

class ExpressTest extends TestCase
{
    public function testQueryExpressWithInvalidCode()
    {
        $express = new Express();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid express code abc');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithRequiredPostId()
    {
        $express = new Express();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Post ID is required');
        $express->query('shunfeng', '');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())// 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(Express::class)->makePartial();
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

        $express = \Mockery::mock(Express::class)->makePartial();
        $express->allows()->getHttpClient()->andReturn($client);
        $this->assertSame(['message' => 'ok'], $express->query('shunfeng', '123'));
    }
}