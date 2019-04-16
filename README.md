# express

快递查询（基于快递100），能够让你省钱的对接方式。

[![Build Status](https://travis-ci.org/zhlhuang/express.svg?branch=master)](https://travis-ci.org/zhlhuang/express)
[![StyleCI](https://github.styleci.io/repos/151804146/shield?branch=master)](https://github.styleci.io/repos/151804146)
## 安装

```shell
$ composer require zhlhuang/express -vvv
```

## 使用

### 免费接口
```php
use Zhlhuang\Express\Express;

$express = new Express();
$express->query('jd', '78785333107', 'json');
```
### 快递100企业版本

**PS:优先调用免费版本，查不到信息或者调用失败的时候会继续调用企业版本**

```php
use Zhlhuang\Express\Express;

$kuaidiFree = new KuaidiFree();
//实例化企业版本的对象
$kuaidiCompany = new KuaidiCompany([
    'customer' => 'customercustomer',
    'key'      => 'keykeykey'
]);
//实例化聚合数据
$juheExp = new JuheExp([
    'customer' => 'customercustomer',
    'key'      => 'keykeykey'
]);
$express = new Express([$kuaidiFree, $kuaidiCompany, $juheExp]);
$express->query('jd', '78785333107', 'json');
```
## 正常响应
```json
{
	"message": "ok",
	"nu": "532071843804", //单号
	"ischeck": "1",//是否签收标记
	"condition": "F00",
	"com": "lianhaowuliu", //快递公司编码,一律用小写字母
	"status": "200",
	"state": "3", //快递单当前签收状态，包括0在途中、1已揽收、2疑难、3已签收
	"data": [{
		"time": "2018-07-17 11:26:35", //时间，原始格式
		"ftime": "2018-07-17 11:26:35", //格式化后时间
		"context": "已签收,签收人是【】签收图片",
		"location": ""
	}, {
		"time": "2018-07-17 08:30:20",
		"ftime": "2018-07-17 08:30:20",
		"context": "【龙华】的【梁献新】正在派件,扫描员是【梁献新】",
		"location": ""
	}, {
		"time": "2018-07-17 06:37:45",
		"ftime": "2018-07-17 06:37:45",
		"context": "快件到达【龙华】上一站是【深圳分拨中心】,扫描员是【杨广 】",
		"location": ""
	}, {
		"time": "2018-07-17 01:10:14",
		"ftime": "2018-07-17 01:10:14",
		"context": "由【SZB046】,扫描发往 【龙华】",
		"location": ""
	}, {
		"time": "2018-07-17 01:00:07",
		"ftime": "2018-07-17 01:00:07",
		"context": "快件到达【深圳分拨中心】上一站是【深圳分拨中心】,扫描员是【陈封优 】",
		"location": ""
	}]
}
```

## 快递公司编码

```
申通="shentong" 
EMS="ems" 
顺丰="shunfeng" 
圆通="yuantong" 
中通="yuantong" 
韵达="yunda" 
天天="tiantian" 
汇通="huitongkuaidi" 
全峰="quanfengkuaidi" 
德邦="debangwuliu" 
宅急送="zhaijisong"
京东="jd"
```

## License

MIT