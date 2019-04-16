<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-04-16
 * Time: 10:13.
 */

namespace Zhlhuang\Express\Platform;

use GuzzleHttp\Client;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Exceptions\NoRecordException;

class JuheExp
{
    private $juheShippingCode = [
        'shentong'       => 'sto', //申通
        'ems'            => 'ems', //EMS
        'shunfeng'       => 'sf', //顺丰
        'yuantong'       => 'yt', //圆通
        'zhongtong'      => 'zto', //中通
        'tiantian'       => 'tt', //天天
        'huitongkuaidi'  => 'ht', //汇通
        'quanfengkuaidi' => 'qf', //全峰快递
        'debangwuliu'    => 'db', //德邦物流
        'zhaijisong'     => 'db', //宅急送
        'jd'             => 'jd', //京东
    ];
    private $url = 'http://v.juhe.cn/exp/index';
    protected $guzzleOptions = [];
    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function query($expressCode, $postId = '')
    {
        if (empty($this->config['key'])) {
            throw new InvalidArgumentException('config key is required');
        }

        if (empty($this->juheShippingCode[$expressCode])) {
            throw new InvalidArgumentException('Juhe shipping code is invalid');
        }
        $juheExpressCode = $this->juheShippingCode[$expressCode];
        $postData = [
            'key' => $this->config['key'],
            'no'  => $postId,
            'com' => $juheExpressCode,
        ];

        try {
            $response = $this->getHttpClient()->get($this->url, ['query' => $postData])->getBody()->getContents();
            $response = \json_decode($response, true);
            if (!empty($response['error_code']) && $response['error_code'] != 0) {
                throw new NoRecordException($response['reason'], 404);
            }
            //统一返回格式
            $result = [
                'message'   => $response['reason'],
                'state'     => 0,
                'status'    => 200,
                'condition' => 'F00',
                'ischeck'   => 0,
                'com'       => $expressCode,
                'nu'        => $postId,
                'data'      => [],
            ];
            foreach ($response['result']['list'] as $value) {
                $result['data'][] = [
                    'context' => $value['datetime'],
                    'time'    => $value['remark'],
                    'ftime'   => $value['datetime'],
                ];
            }

            return $result;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
