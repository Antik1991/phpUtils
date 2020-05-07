<?php

namespace PhpUtils;

use PHPUnit\Framework\TestCase;

class HttpUtilTest extends TestCase
{
    public function testCurl()
    {
        $rs = HttpUtil::curl('https://www.zhihu.com/api/v3/feed/topstory/hot-lists/total?limit=50&desktop=true');
        $this->assertEquals(200, $rs->status ?? 0);
    }

}