# express

快递查询（基于快递100）

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
	"nu": "78785333107",
	"ischeck": "1",
	"condition": "F00",
	"com": "jd",
	"status": "200",
	"state": "3",
	"data": [{
		"time": "2018-09-30 12:58:34",
		"ftime": "2018-09-30 12:58:34",
		"context": "订单已由本感谢您在京东购物，欢迎您再次光临！",
		"location": ""
	}, {
		"time": "2018-09-30 08:38:23",
		"ftime": "2018-09-30 08:38:23",
		"context": "配送员开始配送，请您准备收货，配送员，罗xx，手机号，xxx",
		"locime": "2018-09-30 07:53:24",
		"context": "货物已分配，等待配送",
		"location": ""
	}, {
		"time": "2018-09-30 07:53:23",
		"ftime": "2018-09-30 07:53:23",
		"context": "货物已到达【江门环市站】",
		"location": ""
	}, {
		"time",
		"ftime": "2018-09-30 05:47:00",
		"context": "货物已完成分拣，离开【中山分拨中心】",
		"location": ""
	}, {
		"time": "2018-09-30 04:24:43",
		"ftime": "2018-09-30 04:24:43",
		"context": "货物已到达【中山分拨中心】09-29 21:50:43",
		"ftime": "2018-09-29 21:50:43",
		"context": "货物已完成分拣，离开【广州亚一分拣中心】",
		"location": ""
	}, {
		"time": "2018-09-29 21:38:43",
		"ftime": "2018-09-29 21:38:43",
		"context": "货物已交time": "2018-09-29 21:38:43",
		"ftime": "2018-09-29 21:38:43",
		"context": "货物已到达【广州亚一分拣中心】",
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