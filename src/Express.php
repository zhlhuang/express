<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2018/10/6
 * Time: 11:02.
 */

namespace Zhlhuang\Express;

use Zhlhuang\Express\Exceptions\InvalidArgumentException;
use Zhlhuang\Express\Exceptions\NoRecordException;
use Zhlhuang\Express\Platform\KuaidiFree;

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
    protected $platform = [
    ];

    function __construct($platform = [])
    {
        if ($platform) {
            $this->platform = $platform;
        } else {
            //默认使用快递100免费模式
            $this->platform = [
                new KuaidiFree()
            ];
        }
    }

    /**
     * @param        $expressCode
     * @param string $postId
     * @param string $format
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     * @return string
     */
    public function query($expressCode, $postId = '', $format = 'array')
    {
        if (!\in_array(\strtolower($expressCode), $this->shippingCode)) {
            throw new InvalidArgumentException('Invalid express code '.$expressCode);
        }
        if (!$postId) {
            throw new InvalidArgumentException('Post ID is required');
        }
        foreach ($this->platform as $key => $platform) {
            try {
                $response = $platform->query($expressCode, $postId);
                return 'array' === $format ? $response : \json_encode($response);
            } catch (\Exception $e) {
                //如果接口有异常则不抛出继续下一个平台调用
                if ($key == count($this->platform) - 1) {
                    //最后一个平台调用异常
                    throw $e;
                }
            }
        }
        throw new NoRecordException('查不到该快递信息', 404);
    }
}
