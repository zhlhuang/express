# express

快递查询（基于快递100）

[![Build Status](https://travis-ci.org/zhlhuang/express.svg?branch=master)](https://travis-ci.org/zhlhuang/express)

## 安装

```shell
$ composer require zhlhuang/express -vvv
```

## 使用

```php
use Zhlhuang\Express\Express;

$express = new Express();
$express->query('jd', '78785333107', 'json');
```

## 正常响应
```json
{
	"message": "ok",
	"nu": "532071843804",
	"ischeck": "1",
	"condition": "F00",
	"com": "lianhaowuliu",
	"status": "200",
	"state": "3",
	"data": [{
		"time": "2018-07-17 11:26:35",
		"ftime": "2018-07-17 11:26:35",
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