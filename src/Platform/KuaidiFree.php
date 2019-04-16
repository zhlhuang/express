<?php

/**
 * 快递100免费接口
 * User: zhlhuang
 * Date: 2018/11/20
 * Time: 13:53.
 */

namespace Zhlhuang\Express\Platform;

use GuzzleHttp\Client;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\NoRecordException;

class KuaidiFree
{
    private $url = 'http://www.kuaidi100.com/query';
    protected $guzzleOptions = [];

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
        $query = array_filter([
            'type'   => \strtolower($expressCode),
            'postid' => $postId,
        ]);

        try {
            $response = $this->getHttpClient()->get($this->url, [
                'query' => $query,
            ])->getBody()->getContents();
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
