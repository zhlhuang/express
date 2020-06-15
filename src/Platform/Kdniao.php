<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-06-15
 * Time: 10:24.
 */

namespace Zhlhuang\Express\Platform;

use GuzzleHttp\Client;
use Zhlhuang\Express\Exceptions\HttpException;
use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Exceptions\NoRecordException;

class Kdniao
{
    private $kdniaoShippingCode = [
        'shentong'      => 'STO', //申通
        'ems'           => 'EMS', //EMS
        'shunfeng'      => 'SF', //顺丰
        'yuantong'      => 'YTO', //圆通
        'zhongtong'     => 'ZTO', //中通
        'tiantian'      => 'HHTT', //天天
        'huitongkuaidi' => 'HTKY', //汇通
        'debangwuliu'   => 'DBL', //德邦物流
        'zhaijisong'    => 'ZJS', //宅急送
        'jd'            => 'JDKY', //京东
    ];
    private $url = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
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
        if (empty($this->config['key']) || empty($this->config['EBusinessID'])) {
            throw new InvalidArgumentException('config key and EBusinessID is required');
        }

        if (empty($this->kdniaoShippingCode[$expressCode])) {
            throw new InvalidArgumentException('Kdniao shipping code is invalid');
        }
        $kdniaoExpressCode = $this->kdniaoShippingCode[$expressCode];

        $requestData = [
            'OrderCode'    => '',
            'ShipperCode'  => $kdniaoExpressCode,
            'LogisticCode' => $postId
        ];
        $requestDataString = \json_encode($requestData);
        $dataSign = urlencode(base64_encode(md5($requestDataString.$this->config['key'])));
        $postData = [
            'RequestData' => $requestDataString,
            'EBusinessID' => $this->config['EBusinessID'],
            'RequestType' => 1002,
            'DataSign'    => $dataSign
        ];
        try {
            $response = $this->getHttpClient()->get($this->url, ['query' => $postData])->getBody()->getContents();
            $response = \json_decode($response, true);
            if (!empty($response['error_code']) && $response['error_code'] != 0) {
                throw new NoRecordException($response['reason'], 404);
            }
            //统一返回格式
            $result = [
                'message'   => empty($response['Reason']) ? '' : $response['Reason'],
                //除了3为已签收状态，其余都是0在途中
                'state'     => !empty($response['State']) && $response['State'] == 3 ? $response['State'] : 0,
                'status'    => 200,
                'condition' => 'F00',
                'ischeck'   => 0,
                'com'       => $expressCode,
                'nu'        => $postId,
                'data'      => [],
            ];
            if (!empty($response['Traces'])) {
                foreach ($response['Traces'] as $value) {
                    $result['data'][] = [
                        'context' => $value['AcceptStation'],
                        'time'    => $value['AcceptTime'],
                        'ftime'   => $value['AcceptTime'],
                    ];
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
