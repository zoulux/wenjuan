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
$client = new  \Jake\Wenjuan\WenJuanClient([
    'app_key' => '####',
    'app_secret' => '####'
]); 
$res= $client->sheetGet('12345','12345');
print_r($res);
```

更多文档参考 [问卷网文档](https://www.wenjuan.com/open/devdocument_v3_4_4)

## License
MIT