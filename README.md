# wenjuan

简化问卷网接口对接，免去签名、参数拼接等。

## Requirement
- PHP >= 7.0
- [Composer](https://getcomposer.org/)

## Installation
```bash
$ composer require "jake/wenjuan" -vvv
```

## Usage
基本使用

```php
$config=[
    'app_key' => '####',
    'app_secret' => '####'
];

$client = new  \Jake\Wenjuan\WenJuanClient($config); 
$res= $client->sheetGet('12345','12345');
print_r($res);
```

config 中可选参数还有
- mode 
DEV 指向测试环境 PRO 指向线上环境

- domain
可自定义问卷网的地址域

- timestamp
方便测试，可以指定 timestamp

更多文档参考 [问卷网文档](https://www.wenjuan.com/open/devdocument_v3_4_4)

## License
MIT