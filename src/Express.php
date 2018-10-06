<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/10/6
 * Time: 11:02.
 */

namespace Zhlhuang\Express;


use GuzzleHttp\Client;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;

class Express
{
    protected $shippingCode = [
        'shentong', //申通
        'ems', //EMS
        'shunfeng', //顺丰
        'yuantong', //圆通
        'zhongtong', //中通
        'tiantian', //天天
        'huitongkuaidi', //汇通
        'quanfengkuaidi', //全峰快递
        'debangwuliu', //德邦物流
        'zhaijisong', //宅急送
        'jd', //京东
    ];

    protected $guzzleOptions = [];

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param        $expressCode
     * @param string $postId
     * @param string $format
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     *
     * @return mixed|string
     */
    public function query($expressCode, $postId = '', $format = 'array')
    {

        if (!\in_array(\strtolower($expressCode), $this->shippingCode)) {
            throw new InvalidArgumentException('Invalid express code '.$expressCode);
        }
        if (!$postId) {
            throw new InvalidArgumentException('Post ID is required');
        }

        $url = 'http://www.kuaidi100.com/query';

        $query = array_filter([
            'type'   => \strtolower($expressCode),
            'postid' => $postId,
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return 'array' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}