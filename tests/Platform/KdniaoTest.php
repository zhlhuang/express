<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-06-15
 * Time: 11:01.
 */

namespace Zhlhuang\Express\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Platform\Kdniao;

class KdniaoTest extends TestCase
{
    public function testQueryExpressWithInvalidConfigKey()
    {
        $express = new Kdniao();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('config key and EBusinessID is required');
        $express->query('abc', '123');
        $this->fail('Faild to assert');
    }

    public function testQueryExpressWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())// 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock(Kdniao::class)->makePartial();
        $w->setConfig([
            'EBusinessID' => '123',
            'key'         => 'abc',
        ]);
        $w->allows()->getHttpClient()->andReturn($client);
        // 接着需要断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');
        $w->query('shunfeng', '123');
    }

    public function testExpressQuery()
    {
        $response = new Response(200, [], '{"Success":false,"Reason":"非法参数[DataSign[OGJjNDg1NWJmMjJlMDYwNjZmMTdiODY3MWZmMWI2ZWE=]不合法.]"}');
        $client = \Mockery::mock(Client::class);
        $requestData = [
            'OrderCode'    => '',
            'ShipperCode'  => 'ZTO',
            'LogisticCode' => '222',
        ];
        $requestDataString = \json_encode($requestData);
        $dataSign = urlencode(base64_encode(md5($requestDataString.'key-123-key')));
        $client->allows()->get('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', [
            'query' => [
                'RequestData' => $requestDataString,
                'EBusinessID' => '123',
                'RequestType' => 1002,
                'DataSign'    => $dataSign,
            ],
        ])->andReturn($response);

        $express = \Mockery::mock(Kdniao::class)->makePartial();
        $express->setConfig([
            'EBusinessID' => '123',
            'key'         => 'key-123-key',
        ]);
        $express->allows()->getHttpClient()->andReturn($client);
        $this->assertSame([
            'message'   => '非法参数[DataSign[OGJjNDg1NWJmMjJlMDYwNjZmMTdiODY3MWZmMWI2ZWE=]不合法.]',
            'state'     => 0,
            'status'    => 200,
            'condition' => 'F00',
            'ischeck'   => 0,
            'com'       => 'zhongtong',
            'nu'        => '222',
            'data'      => [],
        ], $express->query('zhongtong', '222'));
    }
}
