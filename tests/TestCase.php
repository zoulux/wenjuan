<?php


namespace Jake\Wenjuan\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Jake\Wenjuan\WenJuanClient;

class TestCase extends BaseTestCase
{
    public function testSign()
    {
        $client = new WenJuanClient([
            'app_key' => 'wjaya5uaqqd0anvq11',
            'app_secret' => 'cc063165ef55809cdf6373d979302e3c',
            'timestamp' => 1576570318,
        ]);

        $realSign = '0924b4962681d57952728c558407e046';
        $urlInfo = parse_url($client->login('111'));
        $query = [];
        parse_str($urlInfo['query'], $query);;

        $this->assertEquals($query['wj_signature'], $realSign);
    }
}