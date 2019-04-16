<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/11/20
 * Time: 15:27.
 */

namespace Zhlhuang\Express\Platform;

use GuzzleHttp\Client;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Exceptions\NoRecordException;

class KuaidiCompany
{
    private $url = 'https://poll.kuaidi100.com/poll/query.do';
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

        if (empty($this->config['customer'])) {
            throw new InvalidArgumentException('config customer is required');
        }

        $params = \json_encode([
            'com' => $expressCode,
            'num' => $postId,
        ]);
        $sign = md5($params.$this->config['key'].$this->config['customer']);
        $postData = [
            'customer' => $this->config['customer'],
            'sign'     => strtoupper($sign),
            'param'    => $params,
        ];

        try {
            $response = $this->getHttpClient()->get($this->url, ['query' => $postData])->getBody()->getContents();
            $response = \json_decode($response, true);
            if (!empty($response['status']) && $response['status'] != '200') {
                throw new NoRecordException('查不到该数据', 404);
            }

            //如果返回数据是查不到该数据，同样抛出异常处理
            if (!empty($response['data'][0])) {
                $firstData = $response['data'][0];
                if ($firstData['context'] === '查无结果') {
                    throw new NoRecordException('查不到该数据', 404);
                }
            }

            return $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
